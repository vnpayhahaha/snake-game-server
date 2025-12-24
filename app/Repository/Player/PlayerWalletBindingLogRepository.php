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

namespace App\Repository\Player;

use App\Model\Player\PlayerWalletBindingLog;
use Hyperf\Database\Model\Builder;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;

/**
 * 玩家钱包绑定变更记录表 Repository类
 */
class PlayerWalletBindingLogRepository extends IRepository
{
   public function __construct(
        protected readonly PlayerWalletBindingLog $model
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

        // 群组ID
        if (isset($params['group_id']) && filled($params['group_id'])) {
            $query->where('group_id', '=', $params['group_id']);
        }

        // Telegram用户ID
        if (isset($params['tg_user_id']) && filled($params['tg_user_id'])) {
            $query->where('tg_user_id', '=', $params['tg_user_id']);
        }

        // Telegram用户名
        if (isset($params['tg_username']) && filled($params['tg_username'])) {
            $query->where('tg_username', 'like', '%'.$params['tg_username'].'%');
        }

        // Telegram名字
        if (isset($params['tg_first_name']) && filled($params['tg_first_name'])) {
            $query->where('tg_first_name', 'like', '%'.$params['tg_first_name'].'%');
        }

        // Telegram姓氏
        if (isset($params['tg_last_name']) && filled($params['tg_last_name'])) {
            $query->where('tg_last_name', 'like', '%'.$params['tg_last_name'].'%');
        }

        // 变更前钱包地址（首次绑定为空字符串）
        if (isset($params['old_wallet_address']) && filled($params['old_wallet_address'])) {
            $query->where('old_wallet_address', 'like', '%'.$params['old_wallet_address'].'%');
        }

        // 变更后钱包地址
        if (isset($params['new_wallet_address']) && filled($params['new_wallet_address'])) {
            $query->where('new_wallet_address', 'like', '%'.$params['new_wallet_address'].'%');
        }

        // 变更类型
        if (isset($params['change_type']) && filled($params['change_type'])) {
            $query->where('change_type', '=', $params['change_type']);
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