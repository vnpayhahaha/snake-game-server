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

namespace Plugin\West\SysSettings\Service;

use App\Service\IService;
use Plugin\West\SysSettings\Repository\ConfigGroupRepository as Repository;

/**
 * 参数配置分组表服务类.
 */
final class ConfigGroupService extends IService
{
    public function __construct(
        protected readonly Repository $repository
    ) {}
}
