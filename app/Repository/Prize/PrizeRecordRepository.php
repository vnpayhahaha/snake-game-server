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

namespace App\Repository\Prize;

use App\Model\Prize\PrizeRecord;
use Hyperf\Database\Model\Builder;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;

/**
 * 中奖记录表 Repository类
 */
class PrizeRecordRepository extends IRepository
{
   public function __construct(
        protected readonly PrizeRecord $model
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

        // 开奖流水号(格式: WIN+群ID+日期时间)
        if (isset($params['prize_serial_no']) && filled($params['prize_serial_no'])) {
            $query->where('prize_serial_no', 'like', '%'.$params['prize_serial_no'].'%');
        }

        // 钱包周期（对应wallet_change_count）
        if (isset($params['wallet_cycle']) && filled($params['wallet_cycle'])) {
            $query->where('wallet_cycle', '=', $params['wallet_cycle']);
        }

        // 中奖凭证
        if (isset($params['ticket_number']) && filled($params['ticket_number'])) {
            $query->where('ticket_number', '=', $params['ticket_number']);
        }

        // 中奖节点ID（首）
        if (isset($params['winner_node_id_first']) && filled($params['winner_node_id_first'])) {
            $query->where('winner_node_id_first', '=', $params['winner_node_id_first']);
        }

        // 中奖节点ID（尾）
        if (isset($params['winner_node_id_last']) && filled($params['winner_node_id_last'])) {
            $query->where('winner_node_id_last', '=', $params['winner_node_id_last']);
        }

        // 中奖区间所有节点ID（逗号分割）
        if (isset($params['winner_node_ids']) && filled($params['winner_node_ids'])) {
            $query->where('winner_node_ids', '=', $params['winner_node_ids']);
        }

        // 区间总金额
        if (isset($params['total_amount']) && filled($params['total_amount'])) {
            $query->where('total_amount', '=', $params['total_amount']);
        }

        // 平台抽成
        if (isset($params['platform_fee']) && filled($params['platform_fee'])) {
            $query->where('platform_fee', '=', $params['platform_fee']);
        }

        // 手续费比例（记录当时费率）
        if (isset($params['fee_rate']) && filled($params['fee_rate'])) {
            $query->where('fee_rate', '=', $params['fee_rate']);
        }

        // 奖池金额
        if (isset($params['prize_pool']) && filled($params['prize_pool'])) {
            $query->where('prize_pool', '=', $params['prize_pool']);
        }

        // 派奖金额（奖池-平台抽成）
        if (isset($params['prize_amount']) && filled($params['prize_amount'])) {
            $query->where('prize_amount', '=', $params['prize_amount']);
        }

        // 每人奖金
        if (isset($params['prize_per_winner']) && filled($params['prize_per_winner'])) {
            $query->where('prize_per_winner', '=', $params['prize_per_winner']);
        }

        // 奖池剩余金额（扣除本次中奖后余额）
        if (isset($params['pool_remaining']) && filled($params['pool_remaining'])) {
            $query->where('pool_remaining', '=', $params['pool_remaining']);
        }

        // 中奖人数
        if (isset($params['winner_count']) && filled($params['winner_count'])) {
            $query->where('winner_count', '=', $params['winner_count']);
        }

        // 状态
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', '=', $params['status']);
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
}