<?php

declare(strict_types=1);

namespace App\Model\Snake;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 主键
 * @property int $group_id 群组ID
 * @property int $wallet_cycle 钱包周期（对应wallet_change_count）
 * @property string $ticket_number 购彩凭证(00-99)
 * @property string $ticket_serial_no 凭证流水号(格式: YYYYMMDD-序号)
 * @property string $player_address 玩家钱包地址
 * @property string $player_tg_username Telegram用户名
 * @property int $player_tg_user_id Telegram用户ID
 * @property string $amount 投注金额
 * @property string $tx_hash 交易哈希
 * @property int $block_height 区块高度
 * @property int $daily_sequence 当天第几笔交易（从1开始）
 * @property int $status 状态:1=活跃,2=已中奖,3=未中奖
 * @property int $matched_prize_id 匹配的中奖记录ID
 * @property \Carbon\Carbon $created_at 创建时间
 */
class SnakeNode extends Model
{
    public bool $timestamps = false; // snake_node 表没有 updated_at 字段

    // 状态常量
    public const STATUS_ACTIVE = 1;      // 活跃
    public const STATUS_MATCHED = 2;     // 已中奖
    public const STATUS_UNMATCHED = 3;   // 未中奖（或已归档）

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'snake_node';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [
        'id', 'group_id', 'wallet_cycle', 'ticket_number', 'ticket_serial_no',
        'player_address', 'player_tg_username', 'player_tg_user_id', 'amount',
        'tx_hash', 'block_height', 'daily_sequence', 'status', 'matched_prize_id',
        'created_at'
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer', 'group_id' => 'integer', 'wallet_cycle' => 'integer',
        'player_tg_user_id' => 'integer', 'amount' => 'decimal:8', 'block_height' => 'integer',
        'daily_sequence' => 'integer', 'status' => 'integer', 'matched_prize_id' => 'integer',
        'created_at' => 'datetime'
    ];
}
