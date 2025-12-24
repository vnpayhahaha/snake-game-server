<?php

declare(strict_types=1);

namespace App\Queue;

use App\Service\Snake\SnakeGameService;
use App\Service\Telegram\Bot\CommandEnum;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Process\Annotation\Process;
use Hyperf\Redis\Redis;
use Throwable;

/**
 * TRON 交易处理队列消费者
 * @Process(name="TronTxProcessQueue", num=1)
 */
class TronTxProcessQueue
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    #[Inject]
    private SnakeGameService $snakeGameService;

    #[Inject]
    private Redis $redis;

    public function handle(): void
    {
        $this->logger->info('TronTxProcessQueue consumer started.');

        while (true) {
            try {
                $data = $this->redis->blpop(CommandEnum::TRON_TX_PROCESS_QUEUE_NAME, 0); // 阻塞式弹出，等待消息

                if (empty($data)) {
                    continue;
                }

                $message = json_decode($data[1], true);

                if (!isset($message['tron_transaction_log_id'])) {
                    $this->logger->warning('Received invalid message from TRON_TX_PROCESS_QUEUE.', ['message' => $message]);
                    continue;
                }

                $tronTransactionLogId = (int)$message['tron_transaction_log_id'];

                $this->logger->info(sprintf('Processing TRON transaction log ID: %d', $tronTransactionLogId));

                // 调用 SnakeGameService 处理交易
                $this->snakeGameService->processTronTransaction($tronTransactionLogId);

                $this->logger->info(sprintf('Finished processing TRON transaction log ID: %d', $tronTransactionLogId));

            } catch (Throwable $e) {
                $this->logger->error(sprintf('Error in TronTxProcessQueue consumer: %s', $e->getMessage()), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
                // 异常处理：根据错误类型决定是否重新入队或记录失败日志
                sleep(1); // 避免在循环中不断抛出错误导致CPU飙升
            }
        }
    }
}
