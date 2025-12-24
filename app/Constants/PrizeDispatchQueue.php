<?php

namespace App\Constants;

use App\Constants\Lib\ConstantsOptionTrait;

/**
 * 奖励发放任务队列常量
 */
class PrizeDispatchQueue
{
    use ConstantsOptionTrait;

    // 状态 (1=待处理 2=处理中 3=已完成 4=失败 5=取消)
    public const STATUS_PENDING = 1;
    public const STATUS_PROCESSING = 2;
    public const STATUS_COMPLETED = 3;
    public const STATUS_FAILED = 4;
    public const STATUS_CANCELLED = 5;
    public static array $status_list = [
        self::STATUS_PENDING => 'prize_dispatch_queue.enums.status.1',
        self::STATUS_PROCESSING => 'prize_dispatch_queue.enums.status.2',
        self::STATUS_COMPLETED => 'prize_dispatch_queue.enums.status.3',
        self::STATUS_FAILED => 'prize_dispatch_queue.enums.status.4',
        self::STATUS_CANCELLED => 'prize_dispatch_queue.enums.status.5',
    ];

    // 优先级 (1-10,数字越小优先级越高)
    public const PRIORITY_HIGHEST = 1;
    public const PRIORITY_HIGH = 3;
    public const PRIORITY_NORMAL = 5;
    public const PRIORITY_LOW = 7;
    public const PRIORITY_LOWEST = 10;
    public static array $priority_list = [
        self::PRIORITY_HIGHEST => 'prize_dispatch_queue.enums.priority.1',
        self::PRIORITY_HIGH => 'prize_dispatch_queue.enums.priority.3',
        self::PRIORITY_NORMAL => 'prize_dispatch_queue.enums.priority.5',
        self::PRIORITY_LOW => 'prize_dispatch_queue.enums.priority.7',
        self::PRIORITY_LOWEST => 'prize_dispatch_queue.enums.priority.10',
    ];
}
