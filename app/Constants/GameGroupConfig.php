<?php

namespace App\Constants;

use App\Constants\Lib\ConstantsOptionTrait;

/**
 * 游戏群组配置常量
 */
class GameGroupConfig
{
    use ConstantsOptionTrait;

    // 钱包变更状态 (1=正常 2=变更中)
    public const WALLET_CHANGE_STATUS_NORMAL  = 1;
    public const WALLET_CHANGE_STATUS_CHANGING = 2;
    public static array $wallet_change_status_list = [
        self::WALLET_CHANGE_STATUS_NORMAL  => 'game_group_config.enums.wallet_change_status.1',
        self::WALLET_CHANGE_STATUS_CHANGING => 'game_group_config.enums.wallet_change_status.2',
    ];

    // 状态 (1=正常 0=停用)
    public const STATUS_NORMAL  = 1;
    public const STATUS_DISABLE = 0;
    public static array $status_list = [
        self::STATUS_NORMAL  => 'game_group_config.enums.status.1',
        self::STATUS_DISABLE => 'game_group_config.enums.status.0',
    ];
}
