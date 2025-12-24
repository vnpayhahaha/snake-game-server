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

use App\Model\Prize\PrizeDispatchQueue;
use Hyperf\Database\Model\Builder;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;

/**
 * 奖励发放任务队列表 Repository类
 */
class PrizeDispatchQueueRepository extends IRepository
{
   public function __construct(
        protected readonly PrizeDispatchQueue $model
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

        // 转账记录ID
        if (isset($params['prize_transfer_id']) && filled($params['prize_transfer_id'])) {
            $query->where('prize_transfer_id', '=', $params['prize_transfer_id']);
        }

        // 群组ID
        if (isset($params['group_id']) && filled($params['group_id'])) {
            $query->where('group_id', '=', $params['group_id']);
        }

        // 开奖流水号
        if (isset($params['prize_serial_no']) && filled($params['prize_serial_no'])) {
            $query->where('prize_serial_no', 'like', '%'.$params['prize_serial_no'].'%');
        }

        // 优先级(1-10,数字越小优先级越高)
        if (isset($params['priority']) && filled($params['priority'])) {
            $query->where('priority', '=', $params['priority']);
        }

        // 状态
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', '=', $params['status']);
        }

        // 重试次数
        if (isset($params['retry_count']) && filled($params['retry_count'])) {
            $query->where('retry_count', '=', $params['retry_count']);
        }

        // 最大重试次数
        if (isset($params['max_retry']) && filled($params['max_retry'])) {
            $query->where('max_retry', '=', $params['max_retry']);
        }

        // 任务数据(JSON格式)
        if (isset($params['task_data']) && filled($params['task_data'])) {
            $query->where('task_data', '=', $params['task_data']);
        }

        // 错误信息
        if (isset($params['error_message']) && filled($params['error_message'])) {
            $query->where('error_message', '=', $params['error_message']);
        }

        // 计划执行时间
        if (isset($params['scheduled_at']) && filled($params['scheduled_at']) && is_array($params['scheduled_at']) && count($params['scheduled_at']) == 2) {
            $query->whereBetween(
                'scheduled_at',
                [ $params['scheduled_at'][0], $params['scheduled_at'][1] ]
            );
        }

        // 开始处理时间
        if (isset($params['started_at']) && filled($params['started_at']) && is_array($params['started_at']) && count($params['started_at']) == 2) {
            $query->whereBetween(
                'started_at',
                [ $params['started_at'][0], $params['started_at'][1] ]
            );
        }

        // 完成时间
        if (isset($params['completed_at']) && filled($params['completed_at']) && is_array($params['completed_at']) && count($params['completed_at']) == 2) {
            $query->whereBetween(
                'completed_at',
                [ $params['completed_at'][0], $params['completed_at'][1] ]
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
}