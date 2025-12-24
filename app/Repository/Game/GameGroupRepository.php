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

namespace App\Repository\Game;

use App\Model\Game\GameGroup;
use app\model\ModelGameGroup;
use Hyperf\Database\Model\Builder;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;

/**
 * 游戏群组实时状态表 Repository类
 */
class GameGroupRepository extends IRepository
{
   public function __construct(
        protected readonly GameGroup $model
    ) {}

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        
        // 主键
        if (isset($params['id']) && filled($params['id'])) {
            $query->where('id', '=', $params['id']);
        }

        // 配置表ID
        if (isset($params['config_id']) && filled($params['config_id'])) {
            $query->where('config_id', '=', $params['config_id']);
        }

        // Telegram群组ID
        if (isset($params['tg_chat_id']) && filled($params['tg_chat_id'])) {
            $query->where('tg_chat_id', '=', $params['tg_chat_id']);
        }

        // 当前奖池金额
        if (isset($params['prize_pool_amount']) && filled($params['prize_pool_amount'])) {
            $query->where('prize_pool_amount', '=', $params['prize_pool_amount']);
        }

        // 当前蛇身节点ID（逗号分割）
        if (isset($params['current_snake_nodes']) && filled($params['current_snake_nodes'])) {
            $query->where('current_snake_nodes', '=', $params['current_snake_nodes']);
        }

        // 上次蛇身节点ID（逗号分割）
        if (isset($params['last_snake_nodes']) && filled($params['last_snake_nodes'])) {
            $query->where('last_snake_nodes', '=', $params['last_snake_nodes']);
        }

        // 上次中奖区间节点ID（逗号分割）
        if (isset($params['last_prize_nodes']) && filled($params['last_prize_nodes'])) {
            $query->where('last_prize_nodes', '=', $params['last_prize_nodes']);
        }

        // 上次中奖金额
        if (isset($params['last_prize_amount']) && filled($params['last_prize_amount'])) {
            $query->where('last_prize_amount', '=', $params['last_prize_amount']);
        }

        // 上次中奖地址（多个用逗号分割）
        if (isset($params['last_prize_address']) && filled($params['last_prize_address'])) {
            $query->where('last_prize_address', 'like', '%'.$params['last_prize_address'].'%');
        }

        // 上次开奖流水号
        if (isset($params['last_prize_serial_no']) && filled($params['last_prize_serial_no'])) {
            $query->where('last_prize_serial_no', 'like', '%'.$params['last_prize_serial_no'].'%');
        }

        // 上次中奖时间
        if (isset($params['last_prize_at']) && filled($params['last_prize_at']) && is_array($params['last_prize_at']) && count($params['last_prize_at']) == 2) {
            $query->whereBetween(
                'last_prize_at',
                [ $params['last_prize_at'][0], $params['last_prize_at'][1] ]
            );
        }

        // 乐观锁版本号
        if (isset($params['version']) && filled($params['version'])) {
            $query->where('version', '=', $params['version']);
        }

        // 创建时间
        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [ $params['created_at'][0], $params['created_at'][1] ]
            );
        }

        // 更新时间
        if (isset($params['updated_at']) && filled($params['updated_at']) && is_array($params['updated_at']) && count($params['updated_at']) == 2) {
            $query->whereBetween(
                'updated_at',
                [ $params['updated_at'][0], $params['updated_at'][1] ]
            );
        }

        return $query;
    }

    /**
     * 根据 Telegram Chat ID 获取群组
     */
    public function getByTgChatId(int $tgChatId): ?GameGroup
    {
        return $this->model::query()->where('tg_chat_id', '=', $tgChatId)->first();
    }
}