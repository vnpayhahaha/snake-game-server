<?php
namespace App\Schema\Tron;

use App\Model\Tron\TronTransactionLog;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

/**
 * TRON交易监听日志表
 */
#[Schema(title: 'TronTransactionLogSchema')]
class TronTransactionLogSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'bigint')]
    public string $id;

    #[Property(property: 'group_id', title: '群组ID', type: 'bigint')]
    public string $group_id;

    #[Property(property: 'tx_hash', title: '交易哈希', type: 'varchar')]
    public string $tx_hash;

    #[Property(property: 'from_address', title: '发送地址', type: 'varchar')]
    public string $from_address;

    #[Property(property: 'to_address', title: '接收地址', type: 'varchar')]
    public string $to_address;

    #[Property(property: 'amount', title: '金额(TRX)', type: 'decimal')]
    public string $amount;

    #[Property(property: 'transaction_type', title: '交易类型', type: 'tinyint')]
    public string $transaction_type;

    #[Property(property: 'block_height', title: '区块高度', type: 'bigint')]
    public string $block_height;

    #[Property(property: 'block_timestamp', title: '区块时间戳', type: 'bigint')]
    public string $block_timestamp;

    #[Property(property: 'status', title: '交易状态', type: 'varchar')]
    public string $status;

    #[Property(property: 'is_valid', title: '是否有效交易', type: 'tinyint')]
    public string $is_valid;

    #[Property(property: 'invalid_reason', title: '无效原因', type: 'varchar')]
    public string $invalid_reason;

    #[Property(property: 'processed', title: '是否已处理', type: 'tinyint')]
    public string $processed;

    #[Property(property: 'created_at', title: '创建时间', type: 'datetime')]
    public string $created_at;




    public function __construct(TronTransactionLog $model)
    {
       $this->id = $model->id;
       $this->group_id = $model->group_id;
       $this->tx_hash = $model->tx_hash;
       $this->from_address = $model->from_address;
       $this->to_address = $model->to_address;
       $this->amount = $model->amount;
       $this->transaction_type = $model->transaction_type;
       $this->block_height = $model->block_height;
       $this->block_timestamp = $model->block_timestamp;
       $this->status = $model->status;
       $this->is_valid = $model->is_valid;
       $this->invalid_reason = $model->invalid_reason;
       $this->processed = $model->processed;
       $this->created_at = $model->created_at;

    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id ,'group_id' => $this->group_id ,'tx_hash' => $this->tx_hash ,'from_address' => $this->from_address ,'to_address' => $this->to_address ,'amount' => $this->amount ,'transaction_type' => $this->transaction_type ,'block_height' => $this->block_height ,'block_timestamp' => $this->block_timestamp ,'status' => $this->status ,'is_valid' => $this->is_valid ,'invalid_reason' => $this->invalid_reason ,'processed' => $this->processed ,'created_at' => $this->created_at];
    }
}