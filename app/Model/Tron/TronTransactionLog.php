<?php

declare(strict_types=1);

namespace App\Model\Tron;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 主键
 * @property int $group_id 群组ID
 * @property string $tx_hash 交易哈希
 * @property string $from_address 发送地址
 * @property string $to_address 接收地址
 * @property string $amount 金额(TRX)
 * @property int $transaction_type 交易类型:1=入账,2=出账
 * @property int $block_height 区块高度
 * @property int $block_timestamp 区块时间戳
 * @property string $status 交易状态
 * @property int $is_valid 是否有效交易
 * @property string $invalid_reason 无效原因
 * @property int $processed 是否已处理
 * @property \Carbon\Carbon $created_at 创建时间
 */
class TronTransactionLog extends Model
{
    public bool $timestamps = false;

    // 交易类型
    public const TRANSACTION_TYPE_IN = 1;  // 入账
    public const TRANSACTION_TYPE_OUT = 2; // 出账

    // 交易状态
    public const STATUS_SUCCESS = 'SUCCESS';

    // 处理状态
    public const PROCESSED_NO = 0;
    public const PROCESSED_YES = 1;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'tron_transaction_log';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'group_id', 'tx_hash', 'from_address', 'to_address', 'amount', 'transaction_type', 'block_height', 'block_timestamp', 'status', 'is_valid', 'invalid_reason', 'processed', 'created_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'group_id' => 'integer', 'amount' => 'decimal:8', 'transaction_type' => 'integer', 'block_height' => 'integer', 'block_timestamp' => 'integer', 'is_valid' => 'integer', 'processed' => 'integer', 'created_at' => 'datetime'];
}
