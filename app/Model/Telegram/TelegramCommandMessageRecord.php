<?php

declare(strict_types=1);

namespace App\Model\Telegram;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id 主键
 * @property int $tg_chat_id Telegram群组ID
 * @property int $tg_user_id Telegram用户ID
 * @property string $tg_username Telegram用户名
 * @property string $tg_first_name Telegram名字
 * @property string $tg_last_name Telegram姓氏
 * @property int $tg_message_id Telegram消息ID
 * @property string $command 命令名称（如：/wallet, /snake等）
 * @property string $command_params 命令参数（JSON格式）
 * @property string $request_data 完整请求数据（JSON格式）
 * @property int $status 状态:1=待处理,2=处理中,3=成功,4=失败
 * @property string $response_data 响应数据（JSON格式）
 * @property string $error_message 错误信息
 * @property int $is_admin 是否群管理员
 * @property string $processed_at 处理完成时间
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 */
class TelegramCommandMessageRecord extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'telegram_command_message_record';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'tg_chat_id', 'tg_user_id', 'tg_username', 'tg_first_name', 'tg_last_name', 'tg_message_id', 'command', 'command_params', 'request_data', 'status', 'response_data', 'error_message', 'is_admin', 'processed_at', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'tg_chat_id' => 'integer', 'tg_user_id' => 'integer', 'tg_message_id' => 'integer', 'status' => 'integer', 'is_admin' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
