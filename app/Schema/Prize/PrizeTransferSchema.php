<?php
namespace App\Schema\Prize;

use App\Model\Prize\PrizeTransfer;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

/**
 * 奖金转账记录表
 */
#[Schema(title: 'PrizeTransferSchema')]
class PrizeTransferSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'bigint')]
    public string $id;

    #[Property(property: 'prize_record_id', title: '中奖记录ID', type: 'bigint')]
    public string $prize_record_id;

    #[Property(property: 'prize_serial_no', title: '开奖流水号', type: 'varchar')]
    public string $prize_serial_no;

    #[Property(property: 'node_id', title: '中奖节点ID', type: 'bigint')]
    public string $node_id;

    #[Property(property: 'to_address', title: '收款地址', type: 'varchar')]
    public string $to_address;

    #[Property(property: 'amount', title: '转账金额', type: 'decimal')]
    public string $amount;

    #[Property(property: 'tx_hash', title: '转账交易哈希', type: 'varchar')]
    public string $tx_hash;

    #[Property(property: 'status', title: '状态', type: 'tinyint')]
    public string $status;

    #[Property(property: 'retry_count', title: '重试次数', type: 'int')]
    public string $retry_count;

    #[Property(property: 'error_message', title: '错误信息', type: 'text')]
    public string $error_message;

    #[Property(property: 'created_at', title: '创建时间', type: 'datetime')]
    public string $created_at;

    #[Property(property: 'updated_at', title: '更新时间', type: 'datetime')]
    public string $updated_at;




    public function __construct(PrizeTransfer $model)
    {
       $this->id = $model->id;
       $this->prize_record_id = $model->prize_record_id;
       $this->prize_serial_no = $model->prize_serial_no;
       $this->node_id = $model->node_id;
       $this->to_address = $model->to_address;
       $this->amount = $model->amount;
       $this->tx_hash = $model->tx_hash;
       $this->status = $model->status;
       $this->retry_count = $model->retry_count;
       $this->error_message = $model->error_message;
       $this->created_at = $model->created_at;
       $this->updated_at = $model->updated_at;

    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id ,'prize_record_id' => $this->prize_record_id ,'prize_serial_no' => $this->prize_serial_no ,'node_id' => $this->node_id ,'to_address' => $this->to_address ,'amount' => $this->amount ,'tx_hash' => $this->tx_hash ,'status' => $this->status ,'retry_count' => $this->retry_count ,'error_message' => $this->error_message ,'created_at' => $this->created_at ,'updated_at' => $this->updated_at];
    }
}