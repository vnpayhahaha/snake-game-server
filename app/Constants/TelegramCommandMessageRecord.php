<?php

namespace App\Constants;

use App\Constants\Lib\ConstantsOptionTrait;

/**
 * Telegram命令消息记录常量
 */
class TelegramCommandMessageRecord
{
    use ConstantsOptionTrait;

    // 状态 (1=待处理 2=处理中 3=成功 4=失败)
    public const STATUS_PENDING = 1;
    public const STATUS_PROCESSING = 2;
    public const STATUS_SUCCESS = 3;
    public const STATUS_FAILED = 4;
    public static array $status_list = [
        self::STATUS_PENDING => 'telegram_command_message_record.enums.status.1',
        self::STATUS_PROCESSING => 'telegram_command_message_record.enums.status.2',
        self::STATUS_SUCCESS => 'telegram_command_message_record.enums.status.3',
        self::STATUS_FAILED => 'telegram_command_message_record.enums.status.4',
    ];

    // 是否群管理员 (0=否 1=是)
    public const IS_ADMIN_NO = 0;
    public const IS_ADMIN_YES = 1;
    public static array $is_admin_list = [
        self::IS_ADMIN_NO => 'telegram_command_message_record.enums.is_admin.0',
        self::IS_ADMIN_YES => 'telegram_command_message_record.enums.is_admin.1',
    ];
}
