<?php

namespace App\Constants;

use App\Constants\Lib\ConstantsOptionTrait;

/**
 * 奖金转账记录常量
 */
class PrizeTransfer
{
    use ConstantsOptionTrait;

    // 状态 (1=待处理 2=处理中 3=成功 4=失败)
    public const STATUS_PENDING = 1;
    public const STATUS_PROCESSING = 2;
    public const STATUS_SUCCESS = 3;
    public const STATUS_FAILED = 4;
    public static array $status_list = [
        self::STATUS_PENDING => 'prize_transfer.enums.status.1',
        self::STATUS_PROCESSING => 'prize_transfer.enums.status.2',
        self::STATUS_SUCCESS => 'prize_transfer.enums.status.3',
        self::STATUS_FAILED => 'prize_transfer.enums.status.4',
    ];
}
