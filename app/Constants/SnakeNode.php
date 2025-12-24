<?php

namespace App\Constants;

use App\Constants\Lib\ConstantsOptionTrait;

/**
 * 蛇身节点常量
 */
class SnakeNode
{
    use ConstantsOptionTrait;

    // 状态 (1=活跃 2=已中奖 3=未中奖)
    public const STATUS_ACTIVE = 1;
    public const STATUS_WON = 2;
    public const STATUS_NOT_WON = 3;
    public static array $status_list = [
        self::STATUS_ACTIVE => 'snake_node.enums.status.1',
        self::STATUS_WON => 'snake_node.enums.status.2',
        self::STATUS_NOT_WON => 'snake_node.enums.status.3',
    ];
}
