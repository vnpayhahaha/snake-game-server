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

namespace App\Repository\Tron;

use App\Model\Tron\TronTransactionLog;
use Hyperf\Database\Model\Builder;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;

/**
 * TRON交易监听日志表 Repository类
 */
class TronTransactionLogRepository extends IRepository
{
   public function __construct(
        protected readonly TronTransactionLog $model
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

        // 交易哈希
        if (isset($params['tx_hash']) && filled($params['tx_hash'])) {
            $query->where('tx_hash', 'like', '%'.$params['tx_hash'].'%');
        }

        // 发送地址
        if (isset($params['from_address']) && filled($params['from_address'])) {
            $query->where('from_address', 'like', '%'.$params['from_address'].'%');
        }

        // 接收地址
        if (isset($params['to_address']) && filled($params['to_address'])) {
            $query->where('to_address', 'like', '%'.$params['to_address'].'%');
        }

        // 金额(TRX)
        if (isset($params['amount']) && filled($params['amount'])) {
            $query->where('amount', '=', $params['amount']);
        }

        // 交易类型
        if (isset($params['transaction_type']) && filled($params['transaction_type'])) {
            $query->where('transaction_type', '=', $params['transaction_type']);
        }

        // 区块高度
        if (isset($params['block_height']) && filled($params['block_height'])) {
            $query->where('block_height', '=', $params['block_height']);
        }

        // 区块时间戳
        if (isset($params['block_timestamp']) && filled($params['block_timestamp'])) {
            $query->where('block_timestamp', '=', $params['block_timestamp']);
        }

        // 交易状态
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', 'like', '%'.$params['status'].'%');
        }

        // 是否有效交易
        if (isset($params['is_valid']) && filled($params['is_valid'])) {
            $query->where('is_valid', '=', $params['is_valid']);
        }

        // 无效原因
        if (isset($params['invalid_reason']) && filled($params['invalid_reason'])) {
            $query->where('invalid_reason', 'like', '%'.$params['invalid_reason'].'%');
        }

        // 是否已处理
        if (isset($params['processed']) && filled($params['processed'])) {
            $query->where('processed', '=', $params['processed']);
        }

        // 创建时间
        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [ $params['created_at'][0], $params['created_at'][1] ]
            );
        }

        return $query;
    }
}