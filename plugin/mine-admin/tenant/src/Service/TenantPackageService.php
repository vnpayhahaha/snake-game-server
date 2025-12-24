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

namespace Plugin\MineAdmin\Tenant\Service;

use App\Service\IService;
use Plugin\MineAdmin\Tenant\Model\TenantPackage;
use Plugin\MineAdmin\Tenant\Repository\TenantPackageRepository;

/**
 * @extends IService<TenantPackage>
 */
final class TenantPackageService extends IService
{
    public function __construct(
        protected readonly TenantPackageRepository $repository,
    ) {}

}
