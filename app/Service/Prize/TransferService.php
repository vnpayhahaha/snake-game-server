<?php

declare(strict_types=1);

namespace App\Service\Prize;

use App\Kernel\Wallet\Tron;
use App\Model\Game\GameGroupConfig;
use App\Model\Prize\PrizeRecord;
use App\Model\Prize\PrizeTransfer;
use App\Model\Snake\SnakeNode;
use App\Service\Telegram\Bot\TelegramService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Throwable;

/**
 * 奖金转账服务
 */
class TransferService
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    #[Inject]
    private Tron $tronWallet;

    #[Inject]
    private TelegramService $telegramService;

    /**
     * 发放奖金
     * @param int $prizeRecordId
     * @return bool
     */
    public function dispatchPrize(int $prizeRecordId): bool
    {
        /** @var PrizeRecord $prizeRecord */
        $prizeRecord = PrizeRecord::find($prizeRecordId);
        if (!$prizeRecord || $prizeRecord->status !== PrizeRecord::STATUS_PENDING) {
            $this->logger->warning(sprintf('PrizeRecord %d not found or not in pending status.', $prizeRecordId));
            return false;
        }

        // 获取群组配置（包含热钱包信息）
        /** @var GameGroupConfig $config */
        $config = GameGroupConfig::find($prizeRecord->group_id);
        if (!$config) {
            $this->logger->error(sprintf('GameGroupConfig for group %d not found.', $prizeRecord->group_id));
            return false;
        }

        // 检查热钱包配置
        if (empty($config->hot_wallet_address) || empty($config->hot_wallet_private_key)) {
            $this->logger->error(sprintf('Hot wallet not configured for group %d.', $prizeRecord->group_id));
            $this->sendHotWalletNotConfiguredNotification($config->tg_chat_id);
            return false;
        }

        // 检查热钱包余额
        $requiredAmount = bcmul($prizeRecord->prize_per_winner, (string)$prizeRecord->winner_count, 8);
        $hotWalletBalance = $this->tronWallet->getBalance($config->hot_wallet_address);
        
        if (bccomp($hotWalletBalance, $requiredAmount, 8) < 0) {
            $this->logger->warning(sprintf('Insufficient hot wallet balance for group %d. Required: %s TRX, Available: %s TRX', 
                $prizeRecord->group_id, $requiredAmount, $hotWalletBalance));
            $this->sendInsufficientBalanceNotification($config->tg_chat_id, $requiredAmount, $hotWalletBalance);
            return false;
        }

        // 更新奖金记录状态为转账中
        $prizeRecord->status = PrizeRecord::STATUS_TRANSFERRING;
        $prizeRecord->save();

        // 获取中奖节点
        $winnerNodeIds = json_decode($prizeRecord->winner_node_ids, true);
        $winners = SnakeNode::whereIn('id', $winnerNodeIds)->get();

        $allSuccess = true;
        $successfulTransfers = [];
        $failedTransfers = [];

        Db::beginTransaction();
        try {
            /** @var SnakeNode $winner */
            foreach ($winners as $winner) {
                // 创建转账记录
                $transfer = new PrizeTransfer();
                $transfer->prize_record_id = $prizeRecord->id;
                $transfer->node_id = $winner->id;
                $transfer->to_address = $winner->player_address;
                $transfer->amount = $prizeRecord->prize_per_winner;
                $transfer->status = PrizeTransfer::STATUS_PENDING;
                $transfer->save();

                try {
                    // 执行转账
                    $txHash = $this->tronWallet->sendTransactionWithPrivateKey(
                        $config->hot_wallet_private_key,
                        $config->hot_wallet_address,
                        $winner->player_address,
                        $prizeRecord->prize_per_winner
                    );

                    if ($txHash) {
                        $transfer->tx_hash = $txHash;
                        $transfer->status = PrizeTransfer::STATUS_PROCESSING;
                        $transfer->save();
                        
                        $successfulTransfers[] = [
                            'transfer' => $transfer,
                            'winner' => $winner,
                            'tx_hash' => $txHash
                        ];
                        
                        $this->logger->info(sprintf('Prize transfer successful for node %d, tx_hash: %s', $winner->id, $txHash));
                    } else {
                        throw new \Exception('Failed to get transaction hash from TRON network');
                    }
                } catch (Throwable $e) {
                    $allSuccess = false;
                    $transfer->status = PrizeTransfer::STATUS_FAILED;
                    $transfer->error_message = $e->getMessage();
                    $transfer->retry_count = ($transfer->retry_count ?? 0) + 1;
                    $transfer->save();
                    
                    $failedTransfers[] = [
                        'transfer' => $transfer,
                        'winner' => $winner,
                        'error' => $e->getMessage()
                    ];
                    
                    $this->logger->error(sprintf('Failed to dispatch prize for node %d: %s', $winner->id, $e->getMessage()));
                }
            }

            // 更新总的中奖记录状态
            if ($allSuccess) {
                $prizeRecord->status = PrizeRecord::STATUS_COMPLETED;
            } else if (!empty($successfulTransfers)) {
                $prizeRecord->status = PrizeRecord::STATUS_PARTIAL_FAILURE;
            } else {
                $prizeRecord->status = PrizeRecord::STATUS_FAILED;
            }
            $prizeRecord->save();

            Db::commit();

            // 发送Telegram通知
            $this->sendTransferNotifications($config->tg_chat_id, $prizeRecord, $successfulTransfers, $failedTransfers);

            return $allSuccess;
        } catch (Throwable $e) {
            Db::rollBack();
            $this->logger->error(sprintf('Failed to dispatch prize for record %d: %s', $prizeRecordId, $e->getMessage()));
            
            // 更新状态为失败
            $prizeRecord->status = PrizeRecord::STATUS_FAILED;
            $prizeRecord->save();
            
            return false;
        }
    }

    /**
     * 发送转账通知
     */
    private function sendTransferNotifications(int $chatId, PrizeRecord $prizeRecord, array $successfulTransfers, array $failedTransfers): void
    {
        try {
            // 发送成功转账通知
            foreach ($successfulTransfers as $transfer) {
                $winner = $transfer['winner'];
                $txHash = $transfer['tx_hash'];
                
                $message = [
                    '✅ 奖金已发放！',
                    '',
                    sprintf('流水号：%s', $prizeRecord->prize_serial_no),
                    sprintf('中奖凭证：%s', $prizeRecord->ticket_number),
                    sprintf('收款地址：%s', $winner->player_address),
                    sprintf('到账金额：%s TRX', $prizeRecord->prize_per_winner),
                    sprintf('交易哈希：%s', $txHash),
                    '',
                    '🎉 恭喜中奖，感谢参与！'
                ];

                // 如果玩家绑定了Telegram，则@用户
                if ($winner->player_tg_username) {
                    $message[0] = sprintf('✅ 奖金已发放！@%s', $winner->player_tg_username);
                }

                $this->telegramService->sendMessageProducer($chatId, $message);
            }

            // 发送失败转账通知（仅管理员可见）
            if (!empty($failedTransfers)) {
                $message = [
                    '⚠️ 部分奖金发放失败',
                    '',
                    sprintf('流水号：%s', $prizeRecord->prize_serial_no),
                    '失败详情：'
                ];

                foreach ($failedTransfers as $transfer) {
                    $winner = $transfer['winner'];
                    $error = $transfer['error'];
                    $message[] = sprintf('- %s: %s', $winner->player_address, $error);
                }

                $message[] = '';
                $message[] = '请检查热钱包余额和网络状态';

                $this->telegramService->sendMessageProducer($chatId, $message);
            }
        } catch (Throwable $e) {
            $this->logger->error(sprintf('Failed to send transfer notifications: %s', $e->getMessage()));
        }
    }

    /**
     * 发送热钱包未配置通知
     */
    private function sendHotWalletNotConfiguredNotification(int $chatId): void
    {
        $message = [
            '⚠️ 热钱包未配置',
            '',
            '无法自动发放奖金，请联系管理员配置热钱包地址和私钥。',
            '',
            '配置方法：',
            '1. 在后台管理界面配置热钱包地址',
            '2. 配置热钱包私钥（加密存储）',
            '3. 确保热钱包有足够余额'
        ];

        $this->telegramService->sendMessageProducer($chatId, $message);
    }

    /**
     * 发送余额不足通知
     */
    private function sendInsufficientBalanceNotification(int $chatId, string $requiredAmount, string $availableBalance): void
    {
        $message = [
            '⚠️ 热钱包余额不足',
            '',
            sprintf('需要金额：%s TRX', $requiredAmount),
            sprintf('当前余额：%s TRX', $availableBalance),
            sprintf('缺少金额：%s TRX', bcsub($requiredAmount, $availableBalance, 8)),
            '',
            '请及时向热钱包充值以确保奖金正常发放。',
            '',
            sprintf('热钱包地址：请在后台管理界面查看')
        ];

        $this->telegramService->sendMessageProducer($chatId, $message);
    }

    /**
     * 重试失败的转账
     * @param int $transferId
     * @return bool
     */
    public function retryFailedTransfer(int $transferId): bool
    {
        /** @var PrizeTransfer $transfer */
        $transfer = PrizeTransfer::find($transferId);
        if (!$transfer || $transfer->status !== PrizeTransfer::STATUS_FAILED) {
            return false;
        }

        // 获取相关配置
        $prizeRecord = PrizeRecord::find($transfer->prize_record_id);
        $config = GameGroupConfig::find($prizeRecord->group_id);

        if (!$config || empty($config->hot_wallet_private_key)) {
            return false;
        }

        try {
            $txHash = $this->tronWallet->sendTransactionWithPrivateKey(
                $config->hot_wallet_private_key,
                $config->hot_wallet_address,
                $transfer->to_address,
                $transfer->amount
            );

            if ($txHash) {
                $transfer->tx_hash = $txHash;
                $transfer->status = PrizeTransfer::STATUS_PROCESSING;
                $transfer->retry_count = ($transfer->retry_count ?? 0) + 1;
                $transfer->error_message = null;
                $transfer->save();

                $this->logger->info(sprintf('Retry transfer successful for ID %d, tx_hash: %s', $transferId, $txHash));
                return true;
            }
        } catch (Throwable $e) {
            $transfer->retry_count = ($transfer->retry_count ?? 0) + 1;
            $transfer->error_message = $e->getMessage();
            $transfer->save();

            $this->logger->error(sprintf('Retry transfer failed for ID %d: %s', $transferId, $e->getMessage()));
        }

        return false;
    }
}