<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Service\Prize;

use App\Repository\Prize\PrizeTransferRepository;
use App\Service\IService;

/**
 * 奖金转账记录表服务类
 */
use Hyperf\Di\Annotation\Inject;

final class PrizeTransferService extends IService
{
    #[Inject]
    private \Hyperf\Contract\StdoutLoggerInterface $logger;

    #[Inject]
    private \App\Kernel\Wallet\Tron $tronWallet;

    public function __construct(
        protected readonly PrizeTransferRepository $repository
    ) {}

    /**
     * 发放奖金
     * @param int $prizeRecordId
     * @return bool
     */
    public function dispatchPrize(int $prizeRecordId): bool
    {
        /** @var \App\Model\Prize\PrizeRecord $prizeRecord */
        $prizeRecord = \App\Model\Prize\PrizeRecord::find($prizeRecordId);
        if (!$prizeRecord || $prizeRecord->status !== \App\Model\Prize\PrizeRecord::STATUS_PENDING) {
            $this->logger->warning(sprintf('PrizeRecord %d not found or not in pending status.', $prizeRecordId));
            return false;
        }

        $prizeRecord->status = \App\Model\Prize\PrizeRecord::STATUS_TRANSFERRING;
        $prizeRecord->save();

        $winnerNodeIds = json_decode($prizeRecord->winner_node_ids, true);
        $winners = \App\Model\Snake\SnakeNode::whereIn('id', $winnerNodeIds)->get();

        $allSuccess = true;

        /** @var \App\Model\Snake\SnakeNode $winner */
        foreach ($winners as $winner) {
            $transfer = $this->repository->save([
                'prize_record_id' => $prizeRecord->id,
                'prize_serial_no' => $prizeRecord->prize_serial_no,
                'node_id' => $winner->id,
                'to_address' => $winner->player_address,
                'amount' => $prizeRecord->prize_per_winner,
                'status' => \App\Model\Prize\PrizeTransfer::STATUS_PENDING,
            ]);

            try {
                // 调用 TRON API 进行转账
                // 这是一个简化的示例，实际的转账需要更复杂的错误处理和重试机制
                $txHash = $this->tronWallet->sendTransaction(
                    $transfer->to_address,
                    $transfer->amount
                );

                if ($txHash) {
                    $this->repository->update($transfer->id, [
                        'tx_hash' => $txHash,
                        'status' => \App\Model\Prize\PrizeTransfer::STATUS_PROCESSING, // 假设需要等待确认
                    ]);
                } else {
                    $allSuccess = false;
                    $this->repository->update($transfer->id, [
                        'status' => \App\Model\Prize\PrizeTransfer::STATUS_FAILED,
                        'error_message' => 'Failed to send transaction.',
                    ]);
                }
            } catch (\Throwable $e) {
                $allSuccess = false;
                $this->repository->update($transfer->id, [
                    'status' => \App\Model\Prize\PrizeTransfer::STATUS_FAILED,
                    'error_message' => $e->getMessage(),
                ]);
                $this->logger->error(sprintf('Failed to dispatch prize for transfer ID %d: %s', $transfer->id, $e->getMessage()));
            }
        }

        // 更新总的中奖记录状态
        $prizeRecord->status = $allSuccess ? \App\Model\Prize\PrizeRecord::STATUS_COMPLETED : \App\Model\Prize\PrizeRecord::STATUS_PARTIAL_FAILURE;
        $prizeRecord->save();

        // TODO: 发送 Telegram 通知

        return true;
    }
}