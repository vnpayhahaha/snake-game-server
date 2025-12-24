<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Plugin\MineAdmin\Tenant\Utils;

use Hyperf\Constants\Annotation\Constants;
use Hyperf\Constants\Annotation\Message;
use Hyperf\Constants\ConstantsTrait;
use Hyperf\Swagger\Annotation as OA;

#[Constants]
#[OA\Schema(title: 'ResultCode', type: 'integer', default: 200)]
enum ResultCode: int
{
    use ConstantsTrait;

    #[Message('result.success')]
    case SUCCESS = 200;

    #[Message('result.fail')]
    case FAIL = 500;

    #[Message('tenant.miss_tenant_id')]
    case MISS_TENANT_ID = 1000;

    #[Message('tenant.enums.tenant_status.2')]
    case TENANT_DISABLE = 1001;

    #[Message('tenant.enums.package_status.2')]
    case TENANT_PACKAGE_DISABLE = 1002;

    #[Message('tenant.package_expire')]
    case PACKAGE_EXPIRE = 1003;

    #[Message('tenant.max_account_count')]
    case MAX_ACCOUNT_COUNT = 1004;
}
