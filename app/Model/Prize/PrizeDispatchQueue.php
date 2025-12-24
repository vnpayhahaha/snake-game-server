<?php

declare(strict_types=1);

namespace App\Model\Prize;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 主键
 * @property int $prize_record_id 中奖记录ID
 * @property int $prize_transfer_id 转账记录ID
 * @property int $group_id 群组ID
 * @property string $prize_serial_no 开奖流水号
 * @property int $priority 优先级(1-10,数字越小优先级越高)
 * @property int $status 状态:1=待处理,2=处理中,3=已完成,4=失败,5=取消
 * @property int $retry_count 重试次数
 * @property int $max_retry 最大重试次数
 * @property string $task_data 任务数据(JSON格式)
 * @property string $error_message 错误信息
 * @property string $scheduled_at 计划执行时间
 * @property string $started_at 开始处理时间
 * @property string $completed_at 完成时间
 * @property int $version 乐观锁版本号
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class PrizeDispatchQueue extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'prize_dispatch_queue';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'prize_record_id', 'prize_transfer_id', 'group_id', 'prize_serial_no', 'priority', 'status', 'retry_count', 'max_retry', 'task_data', 'error_message', 'scheduled_at', 'started_at', 'completed_at', 'version', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'prize_record_id' => 'integer', 'prize_transfer_id' => 'integer', 'group_id' => 'integer', 'priority' => 'integer', 'status' => 'integer', 'retry_count' => 'integer', 'max_retry' => 'integer', 'version' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
