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

use App\Model\Game\GameGroupConfig;
use Hyperf\Database\Model\Builder;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;

/**
 * 游戏群组配置表 Repository类
 */
class GameGroupConfigRepository extends IRepository
{
   public function __construct(
        protected readonly GameGroupConfig $model
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

        // 租户ID
        if (isset($params['tenant_id']) && filled($params['tenant_id'])) {
            $query->where('tenant_id', 'like', '%'.$params['tenant_id'].'%');
        }

        // Telegram群组ID
        if (isset($params['tg_chat_id']) && filled($params['tg_chat_id'])) {
            $query->where('tg_chat_id', '=', $params['tg_chat_id']);
        }

        // 群组名称
        if (isset($params['tg_chat_title']) && filled($params['tg_chat_title'])) {
            $query->where('tg_chat_title', 'like', '%'.$params['tg_chat_title'].'%');
        }

        // TRON钱包地址
        if (isset($params['wallet_address']) && filled($params['wallet_address'])) {
            $query->where('wallet_address', 'like', '%'.$params['wallet_address'].'%');
        }

        // 钱包变更次数（用于区分不同钱包周期）
        if (isset($params['wallet_change_count']) && filled($params['wallet_change_count'])) {
            $query->where('wallet_change_count', '=', $params['wallet_change_count']);
        }

        // 待更新的钱包地址
        if (isset($params['pending_wallet_address']) && filled($params['pending_wallet_address'])) {
            $query->where('pending_wallet_address', 'like', '%'.$params['pending_wallet_address'].'%');
        }

        // 钱包变更状态
        if (isset($params['wallet_change_status']) && filled($params['wallet_change_status'])) {
            $query->where('wallet_change_status', '=', $params['wallet_change_status']);
        }

        // 钱包变更开始时间
        if (isset($params['wallet_change_start_at']) && filled($params['wallet_change_start_at']) && is_array($params['wallet_change_start_at']) && count($params['wallet_change_start_at']) == 2) {
            $query->whereBetween(
                'wallet_change_start_at',
                [ $params['wallet_change_start_at'][0], $params['wallet_change_start_at'][1] ]
            );
        }

        // 钱包变更生效时间
        if (isset($params['wallet_change_end_at']) && filled($params['wallet_change_end_at']) && is_array($params['wallet_change_end_at']) && count($params['wallet_change_end_at']) == 2) {
            $query->whereBetween(
                'wallet_change_end_at',
                [ $params['wallet_change_end_at'][0], $params['wallet_change_end_at'][1] ]
            );
        }

        // 热钱包地址（用于转账）
        if (isset($params['hot_wallet_address']) && filled($params['hot_wallet_address'])) {
            $query->where('hot_wallet_address', 'like', '%'.$params['hot_wallet_address'].'%');
        }

        // 热钱包私钥（加密存储）
        if (isset($params['hot_wallet_private_key']) && filled($params['hot_wallet_private_key'])) {
            $query->where('hot_wallet_private_key', 'like', '%'.$params['hot_wallet_private_key'].'%');
        }

        // 投注金额(TRX)
        if (isset($params['bet_amount']) && filled($params['bet_amount'])) {
            $query->where('bet_amount', '=', $params['bet_amount']);
        }

        // 平台手续费比例(默认10%)
        if (isset($params['platform_fee_rate']) && filled($params['platform_fee_rate'])) {
            $query->where('platform_fee_rate', '=', $params['platform_fee_rate']);
        }

        // 状态 1-正常 0-停用
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', '=', $params['status']);
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
     * 根据Telegram群组ID获取配置
     * @param int $chatId
     * @return GameGroupConfig|null
     */
    public function getConfigByChatId(int $chatId): ?GameGroupConfig
    {
        return $this->model->where('tg_chat_id', $chatId)->first();
    }

    /**
     * 根据Telegram群组ID获取或创建配置
     * @param int $chatId
     * @param array $defaultData
     * @return GameGroupConfig
     */
    public function getOrCreateConfigByChatId(int $chatId, array $defaultData = []): GameGroupConfig
    {
        $config = $this->getConfigByChatId($chatId);
        
        if (!$config) {
            $data = array_merge([
                'tg_chat_id' => $chatId,
                'bet_amount' => 5.0,
                'platform_fee_rate' => 0.1,
                'status' => 1,
            ], $defaultData);
            
            $config = $this->model->create($data);
        }
        
        return $config;
    }
}