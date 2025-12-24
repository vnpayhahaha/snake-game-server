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

use App\Model\Game\GameGroupConfigLog;
use Hyperf\Database\Model\Builder;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;

/**
 * 游戏群组配置变更记录表 Repository类
 */
class GameGroupConfigLogRepository extends IRepository
{
   public function __construct(
        protected readonly GameGroupConfigLog $model
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

        // 变更参数（JSON格式，记录本次提交的字段）
        if (isset($params['change_params']) && filled($params['change_params'])) {
            $query->where('change_params', '=', $params['change_params']);
        }

        // 变更前的完整配置（JSON格式）
        if (isset($params['old_config']) && filled($params['old_config'])) {
            $query->where('old_config', '=', $params['old_config']);
        }

        // 变更后的完整配置（JSON格式）
        if (isset($params['new_config']) && filled($params['new_config'])) {
            $query->where('new_config', '=', $params['new_config']);
        }

        // 操作人
        if (isset($params['operator']) && filled($params['operator'])) {
            $query->where('operator', 'like', '%'.$params['operator'].'%');
        }

        // 操作IP
        if (isset($params['operator_ip']) && filled($params['operator_ip'])) {
            $query->where('operator_ip', 'like', '%'.$params['operator_ip'].'%');
        }

        // 变更来源
        if (isset($params['change_source']) && filled($params['change_source'])) {
            $query->where('change_source', '=', $params['change_source']);
        }

        // Telegram消息ID（仅TG指令时有值）
        if (isset($params['tg_message_id']) && filled($params['tg_message_id'])) {
            $query->where('tg_message_id', '=', $params['tg_message_id']);
        }

        // 变更时间
        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [ $params['created_at'][0], $params['created_at'][1] ]
            );
        }

        return $query;
    }
}