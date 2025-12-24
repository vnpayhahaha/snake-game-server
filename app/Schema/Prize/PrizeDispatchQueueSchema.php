<?php
namespace App\Schema\Prize;

use App\Model\Prize\PrizeDispatchQueue;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

/**
 * 奖励发放任务队列表
 */
#[Schema(title: 'PrizeDispatchQueueSchema')]
class PrizeDispatchQueueSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'bigint')]
    public string $id;

    #[Property(property: 'prize_record_id', title: '中奖记录ID', type: 'bigint')]
    public string $prize_record_id;

    #[Property(property: 'prize_transfer_id', title: '转账记录ID', type: 'bigint')]
    public string $prize_transfer_id;

    #[Property(property: 'group_id', title: '群组ID', type: 'bigint')]
    public string $group_id;

    #[Property(property: 'prize_serial_no', title: '开奖流水号', type: 'varchar')]
    public string $prize_serial_no;

    #[Property(property: 'priority', title: '优先级(1-10,数字越小优先级越高)', type: 'tinyint')]
    public string $priority;

    #[Property(property: 'status', title: '状态', type: 'tinyint')]
    public string $status;

    #[Property(property: 'retry_count', title: '重试次数', type: 'int')]
    public string $retry_count;

    #[Property(property: 'max_retry', title: '最大重试次数', type: 'int')]
    public string $max_retry;

    #[Property(property: 'task_data', title: '任务数据(JSON格式)', type: 'text')]
    public string $task_data;

    #[Property(property: 'error_message', title: '错误信息', type: 'text')]
    public string $error_message;

    #[Property(property: 'scheduled_at', title: '计划执行时间', type: 'datetime')]
    public string $scheduled_at;

    #[Property(property: 'started_at', title: '开始处理时间', type: 'datetime')]
    public string $started_at;

    #[Property(property: 'completed_at', title: '完成时间', type: 'datetime')]
    public string $completed_at;

    #[Property(property: 'version', title: '乐观锁版本号', type: 'int')]
    public string $version;

    #[Property(property: 'created_at', title: '创建时间', type: 'datetime')]
    public string $created_at;

    #[Property(property: 'updated_at', title: '更新时间', type: 'datetime')]
    public string $updated_at;




    public function __construct(PrizeDispatchQueue $model)
    {
       $this->id = $model->id;
       $this->prize_record_id = $model->prize_record_id;
       $this->prize_transfer_id = $model->prize_transfer_id;
       $this->group_id = $model->group_id;
       $this->prize_serial_no = $model->prize_serial_no;
       $this->priority = $model->priority;
       $this->status = $model->status;
       $this->retry_count = $model->retry_count;
       $this->max_retry = $model->max_retry;
       $this->task_data = $model->task_data;
       $this->error_message = $model->error_message;
       $this->scheduled_at = $model->scheduled_at;
       $this->started_at = $model->started_at;
       $this->completed_at = $model->completed_at;
       $this->version = $model->version;
       $this->created_at = $model->created_at;
       $this->updated_at = $model->updated_at;

    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id ,'prize_record_id' => $this->prize_record_id ,'prize_transfer_id' => $this->prize_transfer_id ,'group_id' => $this->group_id ,'prize_serial_no' => $this->prize_serial_no ,'priority' => $this->priority ,'status' => $this->status ,'retry_count' => $this->retry_count ,'max_retry' => $this->max_retry ,'task_data' => $this->task_data ,'error_message' => $this->error_message ,'scheduled_at' => $this->scheduled_at ,'started_at' => $this->started_at ,'completed_at' => $this->completed_at ,'version' => $this->version ,'created_at' => $this->created_at ,'updated_at' => $this->updated_at];
    }
}