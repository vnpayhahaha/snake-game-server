<?php

declare(strict_types=1);

namespace App\Enums;

use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;
use Hyperf\Constants\EnumConstantsTrait;

#[Constants]
enum ErrorCode: int
{
    use EnumConstantsTrait;
    #[Message("Server Error！")]
    case SERVER_ERROR = 500;

    #[Message("系统参数错误")]
    case SYSTEM_INVALID = 700;

    #[Message('result.enum_not_found')]
    case ENUM_NOT_FOUND = 102001;
}
