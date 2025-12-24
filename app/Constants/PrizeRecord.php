<?php

namespace App\Constants;

use App\Constants\Lib\ConstantsOptionTrait;

/**
 * 中奖记录常量
 */
class PrizeRecord
{
    use ConstantsOptionTrait;

    // 状态 (1=待处理 2=转账中 3=已完成 4=失败 5=部分失败)
    public const STATUS_PENDING = 1;
    public const STATUS_TRANSFERRING = 2;
    public const STATUS_COMPLETED = 3;
    public const STATUS_FAILED = 4;
    public const STATUS_PARTIAL_FAILED = 5;
    public static array $status_list = [
        self::STATUS_PENDING => 'prize_record.enums.status.1',
        self::STATUS_TRANSFERRING => 'prize_record.enums.status.2',
        self::STATUS_COMPLETED => 'prize_record.enums.status.3',
        self::STATUS_FAILED => 'prize_record.enums.status.4',
        self::STATUS_PARTIAL_FAILED => 'prize_record.enums.status.5',
    ];

    // 中奖人数 (默认2人)
    public const WINNER_COUNT_TWO = 2;
    public const WINNER_COUNT_ONE = 1;
    public static array $winner_count_list = [
        self::WINNER_COUNT_ONE => 'prize_record.enums.winner_count.1',
        self::WINNER_COUNT_TWO => 'prize_record.enums.winner_count.2',
    ];
}
