<?php

declare(strict_types=1);

namespace App\Model\Prize;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 主键
 * @property int $prize_record_id 中奖记录ID
 * @property string $prize_serial_no 开奖流水号
 * @property int $node_id 中奖节点ID
 * @property string $to_address 收款地址
 * @property string $amount 转账金额
 * @property string $tx_hash 转账交易哈希
 * @property int $status 状态:1=待处理,2=处理中,3=成功,4=失败
 * @property int $retry_count 重试次数
 * @property string $error_message 错误信息
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class PrizeTransfer extends Model
{
    // 状态常量
    public const STATUS_PENDING = 1;
    public const STATUS_PROCESSING = 2;
    public const STATUS_SUCCESS = 3;
    public const STATUS_FAILED = 4;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'prize_transfer';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'prize_record_id', 'prize_serial_no', 'node_id', 'to_address', 'amount', 'tx_hash', 'status', 'retry_count', 'error_message', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'prize_record_id' => 'integer', 'node_id' => 'integer', 'amount' => 'decimal:8', 'status' => 'integer', 'retry_count' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
