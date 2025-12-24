<?php

declare(strict_types=1);

namespace App\Service\Snake;

use App\Model\Game\GameGroup;
use App\Model\Snake\SnakeNode;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;

class SnakeService
{
    #[Inject]
    private StdoutLoggerInterface $logger;

    /**
     * 获取当前蛇身信息（英文）
     */
    public function getCurrentSnakeInfo(int $groupId): array
    {
        try {
            /** @var GameGroup $gameGroup */
            $gameGroup = GameGroup::where('id', $groupId)->first();
            if (!$gameGroup) {
                return ['❌ Game not found for this group'];
            }

            $snakeNodeIds = json_decode($gameGroup->current_snake_nodes, true);
            if (empty($snakeNodeIds)) {
                return [
                    '🐍 Snake Status: Empty',
                    '',
                    'The snake is not yet born.',
                    'Send TRX to the group payment address to start the game!',
                    '',
                    '💡 Use /address to get the payment address'
                ];
            }

            $snakeNodes = SnakeNode::whereIn('id', $snakeNodeIds)->orderBy('id', 'asc')->get();
            $snakeChain = $snakeNodes->pluck('ticket_number')->implode(' ▸ ');

            return [
                '🐍 Current Snake Status:',
                '--------------------',
                sprintf('🔗 Length: %d nodes', count($snakeNodeIds)),
                sprintf('🎯 Chain: %s', $snakeChain),
                sprintf('💰 Prize Pool: %s TRX', $gameGroup->prize_pool_amount ?? '0.00'),
                '',
                sprintf('🎫 Latest Node: %s', $snakeNodes->last()->ticket_number ?? 'N/A'),
                sprintf('⏰ Last Update: %s', $gameGroup->updated_at->format('Y-m-d H:i:s')),
            ];
        } catch (\Throwable $e) {
            $this->logger->error('Get current snake info failed', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);
            return ['❌ Failed to get snake information, please try again later'];
        }
    }

    /**
     * 获取当前蛇身信息（中文）
     */
    public function getCurrentSnakeInfoCn(int $groupId): array
    {
        try {
            /** @var GameGroup $gameGroup */
            $gameGroup = GameGroup::where('id', $groupId)->first();
            if (!$gameGroup) {
                return ['❌ 此群组未找到游戏'];
            }

            $snakeNodeIds = json_decode($gameGroup->current_snake_nodes, true);
            if (empty($snakeNodeIds)) {
                return [
                    '🐍 蛇身状态：空',
                    '',
                    '蛇身尚未诞生。',
                    '向群组收款地址发送TRX开始游戏！',
                    '',
                    '💡 使用 /收款地址 获取收款地址'
                ];
            }

            $snakeNodes = SnakeNode::whereIn('id', $snakeNodeIds)->orderBy('id', 'asc')->get();
            $snakeChain = $snakeNodes->pluck('ticket_number')->implode(' ▸ ');

            return [
                '🐍 当前蛇身状态：',
                '--------------------',
                sprintf('🔗 长度：%d 个节点', count($snakeNodeIds)),
                sprintf('🎯 链条：%s', $snakeChain),
                sprintf('💰 奖池：%s TRX', $gameGroup->prize_pool_amount ?? '0.00'),
                '',
                sprintf('🎫 最新节点：%s', $snakeNodes->last()->ticket_number ?? '无'),
                sprintf('⏰ 最后更新：%s', $gameGroup->updated_at->format('Y-m-d H:i:s')),
            ];
        } catch (\Throwable $e) {
            $this->logger->error('获取当前蛇身信息失败', [
                'group_id' => $groupId,
                'error' => $e->getMessage()
            ]);
            return ['❌ 获取蛇身信息失败，请稍后重试'];
        }
    }
}
