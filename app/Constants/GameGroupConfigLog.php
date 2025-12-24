<?php

namespace App\Constants;

use App\Constants\Lib\ConstantsOptionTrait;

/**
 * 游戏群组配置变更记录常量
 */
class GameGroupConfigLog
{
    use ConstantsOptionTrait;

    // 变更来源 (1=后台编辑 2=TG群指令)
    public const CHANGE_SOURCE_ADMIN = 1;
    public const CHANGE_SOURCE_TG_COMMAND = 2;
    public static array $change_source_list = [
        self::CHANGE_SOURCE_ADMIN => 'game_group_config_log.enums.change_source.1',
        self::CHANGE_SOURCE_TG_COMMAND => 'game_group_config_log.enums.change_source.2',
    ];
}
