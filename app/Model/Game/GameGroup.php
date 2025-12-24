<?php

declare(strict_types=1);

namespace App\Model\Game;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 主键
 * @property int $config_id 配置表ID
 * @property int $tg_chat_id Telegram群组ID
 * @property string $prize_pool_amount 当前奖池金额
 * @property string $current_snake_nodes 当前蛇身节点ID（逗号分割）
 * @property string $last_snake_nodes 上次蛇身节点ID（逗号分割）
 * @property string $last_prize_nodes 上次中奖区间节点ID（逗号分割）
 * @property string $last_prize_amount 上次中奖金额
 * @property string $last_prize_address 上次中奖地址（多个用逗号分割）
 * @property string $last_prize_serial_no 上次开奖流水号
 * @property string $last_prize_at 上次中奖时间
 * @property int $version 乐观锁版本号
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class GameGroup extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'game_group';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'config_id', 'tg_chat_id', 'prize_pool_amount', 'current_snake_nodes', 'last_snake_nodes', 'last_prize_nodes', 'last_prize_amount', 'last_prize_address', 'last_prize_serial_no', 'last_prize_at', 'version', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'config_id' => 'integer', 'tg_chat_id' => 'integer', 'prize_pool_amount' => 'decimal:8', 'last_prize_amount' => 'decimal:8', 'last_prize_at' => 'datetime', 'version' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
