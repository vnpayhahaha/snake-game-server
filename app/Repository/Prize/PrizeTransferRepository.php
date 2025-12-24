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

use App\Model\Prize\PrizeTransfer;
use Hyperf\Database\Model\Builder;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;

/**
 * 奖金转账记录表 Repository类
 */
class PrizeTransferRepository extends IRepository
{
   public function __construct(
        protected readonly PrizeTransfer $model
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

        // 中奖记录ID
        if (isset($params['prize_record_id']) && filled($params['prize_record_id'])) {
            $query->where('prize_record_id', '=', $params['prize_record_id']);
        }

        // 开奖流水号
        if (isset($params['prize_serial_no']) && filled($params['prize_serial_no'])) {
            $query->where('prize_serial_no', 'like', '%'.$params['prize_serial_no'].'%');
        }

        // 中奖节点ID
        if (isset($params['node_id']) && filled($params['node_id'])) {
            $query->where('node_id', '=', $params['node_id']);
        }

        // 收款地址
        if (isset($params['to_address']) && filled($params['to_address'])) {
            $query->where('to_address', 'like', '%'.$params['to_address'].'%');
        }

        // 转账金额
        if (isset($params['amount']) && filled($params['amount'])) {
            $query->where('amount', '=', $params['amount']);
        }

        // 转账交易哈希
        if (isset($params['tx_hash']) && filled($params['tx_hash'])) {
            $query->where('tx_hash', 'like', '%'.$params['tx_hash'].'%');
        }

        // 状态
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', '=', $params['status']);
        }

        // 重试次数
        if (isset($params['retry_count']) && filled($params['retry_count'])) {
            $query->where('retry_count', '=', $params['retry_count']);
        }

        // 错误信息
        if (isset($params['error_message']) && filled($params['error_message'])) {
            $query->where('error_message', '=', $params['error_message']);
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