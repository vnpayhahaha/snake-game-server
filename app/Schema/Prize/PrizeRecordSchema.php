<?php
namespace App\Schema\Prize;

use App\Model\Prize\PrizeRecord;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

/**
 * 中奖记录表
 */
#[Schema(title: 'PrizeRecordSchema')]
class PrizeRecordSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'bigint')]
    public string $id;

    #[Property(property: 'group_id', title: '群组ID', type: 'bigint')]
    public string $group_id;

    #[Property(property: 'prize_serial_no', title: '开奖流水号(格式: WIN+群ID+日期时间)', type: 'varchar')]
    public string $prize_serial_no;

    #[Property(property: 'wallet_cycle', title: '钱包周期（对应wallet_change_count）', type: 'int')]
    public string $wallet_cycle;

    #[Property(property: 'ticket_number', title: '中奖凭证', type: 'char')]
    public string $ticket_number;

    #[Property(property: 'winner_node_id_first', title: '中奖节点ID（首）', type: 'bigint')]
    public string $winner_node_id_first;

    #[Property(property: 'winner_node_id_last', title: '中奖节点ID（尾）', type: 'bigint')]
    public string $winner_node_id_last;

    #[Property(property: 'winner_node_ids', title: '中奖区间所有节点ID（逗号分割）', type: 'text')]
    public string $winner_node_ids;

    #[Property(property: 'total_amount', title: '区间总金额', type: 'decimal')]
    public string $total_amount;

    #[Property(property: 'platform_fee', title: '平台抽成', type: 'decimal')]
    public string $platform_fee;

    #[Property(property: 'fee_rate', title: '手续费比例（记录当时费率）', type: 'decimal')]
    public string $fee_rate;

    #[Property(property: 'prize_pool', title: '奖池金额', type: 'decimal')]
    public string $prize_pool;

    #[Property(property: 'prize_amount', title: '派奖金额（奖池-平台抽成）', type: 'decimal')]
    public string $prize_amount;

    #[Property(property: 'prize_per_winner', title: '每人奖金', type: 'decimal')]
    public string $prize_per_winner;

    #[Property(property: 'pool_remaining', title: '奖池剩余金额（扣除本次中奖后余额）', type: 'decimal')]
    public string $pool_remaining;

    #[Property(property: 'winner_count', title: '中奖人数', type: 'tinyint')]
    public string $winner_count;

    #[Property(property: 'status', title: '状态', type: 'tinyint')]
    public string $status;

    #[Property(property: 'version', title: '乐观锁版本号', type: 'int')]
    public string $version;

    #[Property(property: 'created_at', title: '创建时间', type: 'datetime')]
    public string $created_at;

    #[Property(property: 'updated_at', title: '更新时间', type: 'datetime')]
    public string $updated_at;




    public function __construct(PrizeRecord $model)
    {
       $this->id = $model->id;
       $this->group_id = $model->group_id;
       $this->prize_serial_no = $model->prize_serial_no;
       $this->wallet_cycle = $model->wallet_cycle;
       $this->ticket_number = $model->ticket_number;
       $this->winner_node_id_first = $model->winner_node_id_first;
       $this->winner_node_id_last = $model->winner_node_id_last;
       $this->winner_node_ids = $model->winner_node_ids;
       $this->total_amount = $model->total_amount;
       $this->platform_fee = $model->platform_fee;
       $this->fee_rate = $model->fee_rate;
       $this->prize_pool = $model->prize_pool;
       $this->prize_amount = $model->prize_amount;
       $this->prize_per_winner = $model->prize_per_winner;
       $this->pool_remaining = $model->pool_remaining;
       $this->winner_count = $model->winner_count;
       $this->status = $model->status;
       $this->version = $model->version;
       $this->created_at = $model->created_at;
       $this->updated_at = $model->updated_at;

    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id ,'group_id' => $this->group_id ,'prize_serial_no' => $this->prize_serial_no ,'wallet_cycle' => $this->wallet_cycle ,'ticket_number' => $this->ticket_number ,'winner_node_id_first' => $this->winner_node_id_first ,'winner_node_id_last' => $this->winner_node_id_last ,'winner_node_ids' => $this->winner_node_ids ,'total_amount' => $this->total_amount ,'platform_fee' => $this->platform_fee ,'fee_rate' => $this->fee_rate ,'prize_pool' => $this->prize_pool ,'prize_amount' => $this->prize_amount ,'prize_per_winner' => $this->prize_per_winner ,'pool_remaining' => $this->pool_remaining ,'winner_count' => $this->winner_count ,'status' => $this->status ,'version' => $this->version ,'created_at' => $this->created_at ,'updated_at' => $this->updated_at];
    }
}