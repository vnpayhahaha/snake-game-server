<?php

declare(strict_types=1);

namespace App\Model\Game;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 主键
 * @property int $config_id 配置表ID
 * @property int $tg_chat_id Telegram群组ID
 * @property string $change_params 变更参数（JSON格式，记录本次提交的字段）
 * @property string $old_config 变更前的完整配置（JSON格式）
 * @property string $new_config 变更后的完整配置（JSON格式）
 * @property string $operator 操作人
 * @property string $operator_ip 操作IP
 * @property int $change_source 变更来源:1=后台编辑,2=TG群指令
 * @property int $tg_message_id Telegram消息ID（仅TG指令时有值）
 * @property \Carbon\Carbon $created_at 变更时间
 */
class GameGroupConfigLog extends Model
{
    public bool $timestamps = false;
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'game_group_config_log';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'config_id', 'tg_chat_id', 'change_params', 'old_config', 'new_config', 'operator', 'operator_ip', 'change_source', 'tg_message_id', 'created_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'config_id' => 'integer', 'tg_chat_id' => 'integer', 'change_source' => 'integer', 'tg_message_id' => 'integer', 'created_at' => 'datetime'];
}
