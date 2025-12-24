<?php

namespace App\Constants;

use App\Constants\Lib\ConstantsOptionTrait;

/**
 * 玩家钱包绑定变更记录常量
 */
class PlayerWalletBindingLog
{
    use ConstantsOptionTrait;

    // 变更类型 (1=首次绑定 2=更新绑定)
    public const CHANGE_TYPE_FIRST_BIND = 1;
    public const CHANGE_TYPE_UPDATE_BIND = 2;
    public static array $change_type_list = [
        self::CHANGE_TYPE_FIRST_BIND => 'player_wallet_binding_log.enums.change_type.1',
        self::CHANGE_TYPE_UPDATE_BIND => 'player_wallet_binding_log.enums.change_type.2',
    ];
}
