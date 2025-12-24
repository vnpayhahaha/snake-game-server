<?php

declare(strict_types=1);

namespace App\Queue;

use App\Service\Prize\TransferService;
use App\Service\Telegram\Bot\CommandEnum;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Process\Annotation\Process;
use Hyperf\Redis\Redis;
use Throwable;

/**
 * 奖金发放队列消费者
 * @Process(name="PrizeDispatchQueue", num=1)
 */
class PrizeDispatchQueue
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    #[Inject]
    private TransferService $transferService;

    #[Inject]
    private Redis $redis;

    public function handle(): void
    {
        $this->logger->info('PrizeDispatchQueue consumer started.');

        while (true) {
            try {
                $data = $this->redis->blpop(CommandEnum::PRIZE_DISPATCH_QUEUE_NAME, 0);

                if (empty($data)) {
                    continue;
                }

                $message = json_decode($data[1], true);

                if (!isset($message['prize_record_id'])) {
                    $this->logger->warning('Received invalid message from PRIZE_DISPATCH_QUEUE.', ['message' => $message]);
                    continue;
                }

                $prizeRecordId = (int)$message['prize_record_id'];

                $this->logger->info(sprintf('Processing prize dispatch for record ID: %d', $prizeRecordId));

                // 调用 TransferService 处理奖金发放
                $result = $this->transferService->dispatchPrize($prizeRecordId);

                if ($result) {
                    $this->logger->info(sprintf('Successfully processed prize dispatch for record ID: %d', $prizeRecordId));
                } else {
                    $this->logger->warning(sprintf('Failed to process prize dispatch for record ID: %d', $prizeRecordId));
                }

            } catch (Throwable $e) {
                $this->logger->error(sprintf('Error in PrizeDispatchQueue consumer: %s', $e->getMessage()), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
                sleep(1);
            }
        }
    }
}
