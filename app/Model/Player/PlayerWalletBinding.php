<?php

declare(strict_types=1);

namespace App\Model\Player;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 主键
 * @property int $group_id 群组ID
 * @property int $tg_user_id Telegram用户ID
 * @property string $tg_username Telegram用户名
 * @property string $tg_first_name Telegram名字
 * @property string $tg_last_name Telegram姓氏
 * @property string $wallet_address 绑定的钱包地址
 * @property string $bind_at 首次绑定时间
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class PlayerWalletBinding extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'player_wallet_binding';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'group_id', 'tg_user_id', 'tg_username', 'tg_first_name', 'tg_last_name', 'wallet_address', 'bind_at', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'group_id' => 'integer', 'tg_user_id' => 'integer', 'bind_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
