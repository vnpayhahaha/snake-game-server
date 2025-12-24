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

use App\Model\Player\PlayerWalletBinding;
use Hyperf\Database\Model\Builder;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;

/**
 * 玩家钱包绑定表 Repository类
 */
class PlayerWalletBindingRepository extends IRepository
{
   public function __construct(
        protected readonly PlayerWalletBinding $model
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

        // 绑定的钱包地址
        if (isset($params['wallet_address']) && filled($params['wallet_address'])) {
            $query->where('wallet_address', 'like', '%'.$params['wallet_address'].'%');
        }

        // 首次绑定时间
        if (isset($params['bind_at']) && filled($params['bind_at']) && is_array($params['bind_at']) && count($params['bind_at']) == 2) {
            $query->whereBetween(
                'bind_at',
                [ $params['bind_at'][0], $params['bind_at'][1] ]
            );
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
     * 根据群组和用户获取绑定
     */
    public function getByGroupAndUser(int $groupId, int $tgUserId): ?PlayerWalletBinding
    {
        return $this->model::query()->where('group_id', $groupId)->where('tg_user_id', $tgUserId)->first();
    }
}