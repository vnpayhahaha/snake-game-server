<?php

declare(strict_types=1);

namespace App\Service\Prize;

use App\Model\Game\GameGroup;
use App\Model\Game\GameGroupConfig;
use App\Model\Prize\PrizeRecord;
use App\Model\Snake\SnakeNode;
use Hyperf\Contract\StdoutLoggerInterface;
use App\Service\Telegram\Bot\CommandEnum;
use Hyperf\Di\Annotation\Inject;
use Hyperf\DbConnection\Db;
use Hyperf\Redis\Redis;
use Throwable;

class PrizeService
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    #[Inject]
    private Redis $redis;

    /**
     * 检查新生成的蛇身节点是否触发中奖
     * @param int $groupId
     * @param int $newNodeId
     */
    public function checkMatch(int $groupId, int $newNodeId): void
    {
        $this->logger->info(sprintf('Checking for prize match for group %d with new node %d.', $groupId, $newNodeId));

        /** @var GameGroup $gameGroup */
        $gameGroup = GameGroup::where('id', $groupId)->first();
        if (!$gameGroup) {
            $this->logger->error(sprintf('GameGroup %d not found in checkMatch.', $groupId));
            return;
        }

        $snakeNodeIds = json_decode($gameGroup->current_snake_nodes, true);
        if (count($snakeNodeIds) < 2) {
            $this->logger->info(sprintf('Snake length for group %d is less than 2, no match possible.', $groupId));
            return;
        }

        /** @var SnakeNode[] $snakeNodes */
        $snakeNodes = SnakeNode::whereIn('id', $snakeNodeIds)->orderBy('id', 'asc')->get()->keyBy('id');

        /** @var SnakeNode $newNode */
        $newNode = $snakeNodes[$newNodeId] ?? null;
        if (!$newNode) {
            $this->logger->error(sprintf('New snake node %d not found in snake nodes list.', $newNodeId));
            return;
        }

        // 规则1：连号清空奖池（超级大奖）
        $lastNodeId = $snakeNodeIds[count($snakeNodeIds) - 2]; // 倒数第二个节点
        /** @var SnakeNode $lastNode */
        $lastNode = $snakeNodes[$lastNodeId];

        if ($newNode->ticket_number === $lastNode->ticket_number) {
            $this->logger->info(sprintf('Super prize triggered in group %d! Ticket number: %s', $groupId, $newNode->ticket_number));
            $this->handleSuperPrize($gameGroup, $snakeNodes, $newNode);
            return;
        }

        // 规则2：区间匹配（常规奖励）
        foreach ($snakeNodeIds as $nodeId) {
            if ($nodeId === $newNodeId) {
                continue;
            }
            /** @var SnakeNode $node */
            $node = $snakeNodes[$nodeId];
            if ($newNode->ticket_number === $node->ticket_number) {
                $this->logger->info(sprintf('Regular prize triggered in group %d! Ticket number: %s. Nodes: %d and %d', $groupId, $newNode->ticket_number, $node->id, $newNode->id));
                $this->handleRegularPrize($gameGroup, $snakeNodes, $node, $newNode);
                return; // 一次只处理一个匹配
            }
        }

        $this->logger->info(sprintf('No prize match found for group %d with new node %d.', $groupId, $newNodeId));
    }

    private function handleSuperPrize(GameGroup $gameGroup, $snakeNodes, SnakeNode $newNode): void
    {
        Db::beginTransaction();
        try {
            $gameGroup = GameGroup::lockForUpdate()->find($gameGroup->id);

            // 1. 计算奖金
            $totalAmount = $gameGroup->prize_pool_amount;
            $config = GameGroupConfig::find($gameGroup->config_id);
            $platform_fee = bcmul($totalAmount, $config->platform_fee_rate, 8);
            $prizeAmount = bcsub($totalAmount, $platform_fee, 8);

            // 2. 创建中奖记录
            $prizeRecord = PrizeRecord::create([
                'group_id' => $gameGroup->id,
                'prize_serial_no' => 'WIN' . $gameGroup->id . date('YmdHis'),
                'wallet_cycle' => $config->wallet_change_count,
                'ticket_number' => $newNode->ticket_number,
                'winner_node_id_first' => $snakeNodes->first()->id,
                'winner_node_id_last' => $newNode->id,
                'winner_node_ids' => json_encode($snakeNodes->pluck('id')->all()),
                'total_amount' => $totalAmount,
                'platform_fee' => $platform_fee,
                'fee_rate' => $config->platform_fee_rate,
                'prize_pool' => $totalAmount,
                'prize_amount' => $prizeAmount,
                'prize_per_winner' => $prizeAmount, // 独享
                'pool_remaining' => '0.00000000',
                'winner_count' => 1,
                'status' => PrizeRecord::STATUS_PENDING,
            ]);

            // 3. 更新所有蛇身节点状态
            SnakeNode::whereIn('id', $snakeNodes->pluck('id')->all())->update([
                'status' => SnakeNode::STATUS_MATCHED,
                'matched_prize_id' => $prizeRecord->id,
            ]);

            // 4. 清空游戏群组状态
            $gameGroup->prize_pool_amount = '0.00000000';
            $gameGroup->current_snake_nodes = '[]';
            $gameGroup->last_prize_at = date('Y-m-d H:i:s');
            $gameGroup->save();

            Db::commit();

            // 5. 触发派奖
            $this->redis->lpush(CommandEnum::PRIZE_DISPATCH_QUEUE_NAME, json_encode(['prize_record_id' => $prizeRecord->id]));

        } catch (Throwable $e) {
            Db::rollBack();
            $this->logger->error(sprintf('Failed to handle super prize for group %d: %s', $gameGroup->id, $e->getMessage()));
        }
    }

    private function handleRegularPrize(GameGroup $gameGroup, $snakeNodes, SnakeNode $startNode, SnakeNode $endNode): void
    {
        Db::beginTransaction();
        try {
            $gameGroup = GameGroup::lockForUpdate()->find($gameGroup->id);

            // 1. 提取中奖区间
            $nodeIds = $snakeNodes->pluck('id')->all();
            $startIndex = array_search($startNode->id, $nodeIds);
            $endIndex = array_search($endNode->id, $nodeIds);
            $prizeNodeIds = array_slice($nodeIds, $startIndex, $endIndex - $startIndex + 1);
            $prizeNodes = $snakeNodes->whereIn('id', $prizeNodeIds);

            // 2. 计算奖金
            $totalAmount = $prizeNodes->sum('amount');
            $config = GameGroupConfig::find($gameGroup->config_id);
            $platform_fee = bcmul($totalAmount, $config->platform_fee_rate, 8);
            $prizeAmount = bcsub($totalAmount, $platform_fee, 8);
            $prizePerWinner = bcdiv($prizeAmount, '2', 8);

            // 3. 创建中奖记录
            $prizeRecord = PrizeRecord::create([
                'group_id' => $gameGroup->id,
                'prize_serial_no' => 'WIN' . $gameGroup->id . date('YmdHis'),
                'wallet_cycle' => $config->wallet_change_count,
                'ticket_number' => $endNode->ticket_number,
                'winner_node_id_first' => $startNode->id,
                'winner_node_id_last' => $endNode->id,
                'winner_node_ids' => json_encode($prizeNodeIds),
                'total_amount' => $totalAmount,
                'platform_fee' => $platform_fee,
                'fee_rate' => $config->platform_fee_rate,
                'prize_pool' => $totalAmount,
                'prize_amount' => $prizeAmount,
                'prize_per_winner' => $prizePerWinner,
                'pool_remaining' => bcsub($gameGroup->prize_pool_amount, $totalAmount, 8),
                'winner_count' => 2,
                'status' => PrizeRecord::STATUS_PENDING,
            ]);

            // 4. 更新中奖区间节点状态
            SnakeNode::whereIn('id', $prizeNodeIds)->update([
                'status' => SnakeNode::STATUS_MATCHED,
                'matched_prize_id' => $prizeRecord->id,
            ]);

            // 5. 更新游戏群组状态
            $remainingNodeIds = array_diff($nodeIds, $prizeNodeIds);
            $gameGroup->current_snake_nodes = json_encode(array_values($remainingNodeIds));
            $gameGroup->prize_pool_amount = bcsub($gameGroup->prize_pool_amount, $totalAmount, 8);
            $gameGroup->last_prize_at = date('Y-m-d H:i:s');
            $gameGroup->save();

            Db::commit();

            // 6. 触发派奖
            $this->redis->lpush(CommandEnum::PRIZE_DISPATCH_QUEUE_NAME, json_encode(['prize_record_id' => $prizeRecord->id]));

        } catch (Throwable $e) {
            Db::rollBack();
            $this->logger->error(sprintf('Failed to handle regular prize for group %d: %s', $gameGroup->id, $e->getMessage()));
        }
    }

    public function getHistory(int $groupId): array
    {
        // TODO: 实现查询历史中奖记录的逻辑
        return ['Prize history: (Under development)'];
    }
}
