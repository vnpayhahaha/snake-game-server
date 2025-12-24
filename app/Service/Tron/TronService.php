<?php

declare(strict_types=1);

namespace App\Service\Tron;

use App\Service\Telegram\Bot\CommandEnum;
use App\Kernel\Wallet\Tron;
use App\Model\Game\GameGroupConfig;
use App\Model\Tron\TronTransactionLog;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;
use Throwable;

class TronService
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    #[Inject]
    private Tron $tronWallet;

    #[Inject]
    private TronTransactionLogService $tronTransactionLogService;

    #[Inject]
    private Redis $redis;

    /**
     * 获取指定群组上次处理的区块高度
     * @param int $groupId
     * @return int
     */
    public function getLastProcessedBlock(int $groupId): int
    {
        $lastLog = TronTransactionLog::where('group_id', $groupId)
            ->orderByDesc('block_height')
            ->first();

        return $lastLog ? $lastLog->block_height : (int)env('TRON_START_BLOCK', 50000000);
    }

    /**
     * 监控并处理指定群组的新TRON交易
     * @param GameGroupConfig $config
     * @param int $startBlockHeight
     */
    public function monitorNewTransactions(GameGroupConfig $config, int $startBlockHeight): void
    {
        $walletAddress = $config->wallet_address;
        $groupId = $config->id;

        $this->logger->info(sprintf('Start monitoring new TRON transactions for group %d, address %s from block %d.', $groupId, $walletAddress, $startBlockHeight));

        $transactions = $this->tronWallet->getTransactionsByAddress($walletAddress, $startBlockHeight);

        if (empty($transactions)) {
            $this->logger->info(sprintf('No new transactions found for group %d, address %s.', $groupId, $walletAddress));
            return;
        }

        foreach ($transactions as $txData) {
            try {
                if (TronTransactionLog::where('tx_hash', $txData['tx_hash'])->exists()) {
                    continue;
                }

                $log = $this->tronTransactionLogService->createTransactionLog($groupId, $txData);

                $isValid = $this->validateTransaction($config, $txData);
                $log->is_valid = $isValid['status'];
                $log->invalid_reason = $isValid['reason'];
                $log->save();

                if ($log->is_valid) {
                    $this->redis->lpush(CommandEnum::TRON_TX_PROCESS_QUEUE_NAME, json_encode([
                        'tron_transaction_log_id' => $log->id,
                    ]));
                } else {
                    $this->logger->warning(sprintf('Invalid TRON transaction for group %d: %s. Reason: %s', $groupId, $log->tx_hash, $log->invalid_reason));
                }
            } catch (Throwable $e) {
                $this->logger->error(sprintf('Error processing TRON transaction %s for group %d: %s', $txData['tx_hash'], $groupId, $e->getMessage()));
            }
        }
    }

    /**
     * 验证TRON交易是否符合游戏规则
     * @param GameGroupConfig $config
     * @param array $txData
     * @return array ['status' => bool, 'reason' => string]
     */
    private function validateTransaction(GameGroupConfig $config, array $txData): array
    {
        if (strtoupper($txData['status']) !== 'SUCCESS') {
            return ['status' => false, 'reason' => 'Transaction status is not SUCCESS.'];
        }

        if ($txData['type'] !== 'TransferAsset' && $txData['type'] !== 'TransferContract') {
            return ['status' => false, 'reason' => 'Transaction is not a TRX transfer.'];
        }

        if (strtolower($txData['to_address']) !== strtolower($config->wallet_address)) {
            return ['status' => false, 'reason' => 'Receiver address does not match group wallet address.'];
        }

        $actualAmount = bcmul((string)$txData['amount'], (string)0.000001, 6);
        $betAmount = (string)$config->bet_amount;

        if (bccomp($actualAmount, $betAmount, 6) !== 0) {
            return ['status' => false, 'reason' => sprintf('Amount %s TRX does not match configured bet amount %s TRX.', $actualAmount, $betAmount)];
        }

        return ['status' => true, 'reason' => null];
    }
}
