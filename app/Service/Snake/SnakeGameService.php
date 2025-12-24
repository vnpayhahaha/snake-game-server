<?php

declare(strict_types=1);

namespace App\Service\Snake;

use App\Kernel\Wallet\Tron;
use App\Model\Game\GameGroup;
use App\Model\Game\GameGroupConfig;
use App\Model\Snake\SnakeNode;
use App\Model\Tron\TronTransactionLog;
use App\Service\Player\PlayerWalletService;
use App\Service\Prize\PrizeService;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\DbConnection\Db;
use Throwable;

class SnakeGameService
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    #[Inject]
    private PlayerWalletService $playerWalletService;

    #[Inject]
    private PrizeService $prizeService;

    /**
     * 处理TRON交易日志，执行游戏核心逻辑
     * @param int $tronTransactionLogId
     * @return bool
     */
    public function processTronTransaction(int $tronTransactionLogId): bool
    {
        /** @var TronTransactionLog $log */
        $log = TronTransactionLog::find($tronTransactionLogId);

        if (!$log || $log->processed === TronTransactionLog::PROCESSED_YES) {
            $this->logger->warning(sprintf('TronTransactionLog %d already processed or not found.', $tronTransactionLogId));
            return false;
        }

        if (!$log->is_valid) {
            $this->logger->warning(sprintf('TronTransactionLog %d is not valid, skip processing. Reason: %s', $tronTransactionLogId, $log->invalid_reason));
            $log->processed = TronTransactionLog::PROCESSED_YES;
            $log->save();
            return false;
        }

        /** @var GameGroupConfig $config */
        $config = GameGroupConfig::where('id', $log->group_id)->first();
        if (!$config || $config->status === 0) { // 0 代表停用
            $this->logger->warning(sprintf('GameGroupConfig for group_id %d not found or inactive, skip processing transaction %s.', $log->group_id, $log->tx_hash));
            $log->processed = TronTransactionLog::PROCESSED_YES;
            $log->invalid_reason = 'Game group inactive or not configured.';
            $log->is_valid = false;
            $log->save();
            return false;
        }

        /** @var GameGroup $gameGroup */
        $gameGroup = GameGroup::where('config_id', $config->id)->first();
        if (!$gameGroup) {
            $this->logger->error(sprintf('GameGroup for config_id %d not found, transaction %s cannot be processed.', $config->id, $log->tx_hash));
            // 考虑如何处理这种异常情况，是创建新的 GameGroup 还是标记交易为失败
            $log->processed = TronTransactionLog::PROCESSED_YES;
            $log->invalid_reason = 'Game group real-time state not found.';
            $log->is_valid = false;
            $log->save();
            return false;
        }

        // --- 核心业务逻辑在事务中执行 ---
        Db::beginTransaction();
        try {
            // 确保事务完整性，再次获取最新数据并加锁
            $log = TronTransactionLog::lockForUpdate()->find($tronTransactionLogId);
            if (!$log || $log->processed === TronTransactionLog::PROCESSED_YES) {
                Db::rollBack();
                return false;
            }

            $gameGroup = GameGroup::lockForUpdate()->find($gameGroup->id);
            if (!$gameGroup) {
                Db::rollBack();
                $this->logger->error(sprintf('GameGroup %d not found during transaction lock.', $gameGroup->id));
                return false;
            }

            // 1. 提取购彩凭证
            $ticketNumber = $this->extractTicketNumber($log->tx_hash);

            // 2. 获取玩家钱包绑定信息 (Telegram用户名, ID等)
            $playerBinding = $this->playerWalletService->getPlayerBindingByWalletAddress(
                $log->group_id,
                $log->from_address
            );

            // 3. 生成凭证流水号和当天交易序号
            $ticketSerialNo = $this->generateTicketSerialNo($log->group_id);
            $dailySequence = $this->getDailySequence($log->group_id);

            // 4. 创建蛇身节点 (SnakeNode)
            $snakeNode = new SnakeNode();
            $snakeNode->group_id = $log->group_id;
            $snakeNode->wallet_cycle = $config->wallet_change_count;
            $snakeNode->ticket_number = $ticketNumber;
            $snakeNode->ticket_serial_no = $ticketSerialNo;
            $snakeNode->player_address = $log->from_address;
            $snakeNode->player_tg_username = $playerBinding ? $playerBinding->tg_username : null;
            $snakeNode->player_tg_user_id = $playerBinding ? $playerBinding->tg_user_id : null;
            $snakeNode->amount = $log->amount;
            $snakeNode->tx_hash = $log->tx_hash;
            $snakeNode->block_height = $log->block_height;
            $snakeNode->daily_sequence = $dailySequence;
            $snakeNode->status = SnakeNode::STATUS_ACTIVE;
            $snakeNode->created_at = date('Y-m-d H:i:s');
            $snakeNode->save();

            // 5. 更新游戏群组实时状态 (GameGroup)
            $currentSnakeNodes = json_decode($gameGroup->current_snake_nodes ?? '[]', true);
            $currentSnakeNodes[] = $snakeNode->id;
            $gameGroup->current_snake_nodes = json_encode($currentSnakeNodes);
            $gameGroup->prize_pool_amount = bcadd($gameGroup->prize_pool_amount, $snakeNode->amount, 8);
            $gameGroup->updated_at = date('Y-m-d H:i:s'); // 确保更新时间
            $gameGroup->save();

            // 6. 标记 TronTransactionLog 为已处理
            $log->processed = TronTransactionLog::PROCESSED_YES;
            $log->save();

            Db::commit();

            // 7. 触发中奖检查 (放在事务外，避免阻塞)
            $this->prizeService->checkMatch($gameGroup->id, $snakeNode->id);

            return true;
        } catch (Throwable $e) {
            Db::rollBack();
            $this->logger->error(sprintf('Failed to process TRON transaction %s for group %d: %s', $log->tx_hash, $log->group_id, $e->getMessage()), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            // 标记交易为处理失败，待人工介入或重试
            if ($log) {
                $log->processed = TronTransactionLog::PROCESSED_NO; // 保持未处理状态，可以重试
                $log->invalid_reason = $e->getMessage();
                $log->save();
            }
            return false;
        }
    }

    /**
     * 从交易哈希中提取两位数字作为购彩凭证
     * (根据需求文档中的算法)
     * @param string $txHash
     * @return string
     */
    private function extractTicketNumber(string $txHash): string
    {
        $digits = [];
        for ($i = strlen($txHash) - 1; $i >= 0; $i--) {
            if (is_numeric($txHash[$i])) {
                $digits[] = $txHash[$i];
            }
            if (count($digits) == 2) {
                break;
            }
        }

        if (count($digits) == 0) {
            return '00';
        }
        if (count($digits) == 1) {
            return '0' . $digits[0];
        }
        return $digits[1] . $digits[0]; // 反转顺序，取最后两个数字
    }

    /**
     * 生成凭证流水号：格式 YYYYMMDD-序号
     * @param int $groupId
     * @return string
     */
    private function generateTicketSerialNo(int $groupId): string
    {
        $today = date('Ymd');
        $todayCount = SnakeNode::where('group_id', $groupId)
            ->whereDate('created_at', date('Y-m-d'))
            ->count();
        $sequence = str_pad((string)($todayCount + 1), 3, '0', STR_PAD_LEFT);
        return $today . '-' . $sequence;
    }

    /**
     * 获取当天交易序号
     * @param int $groupId
     * @return int
     */
    private function getDailySequence(int $groupId): int
    {
        return SnakeNode::where('group_id', $groupId)
            ->whereDate('created_at', date('Y-m-d'))
            ->count() + 1;
    }
}
