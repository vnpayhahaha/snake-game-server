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
use Hyperf\Collection\Collection;
use Hyperf\Stringable\Str;
use Plugin\MineAdmin\Tenant\Model\Tenant;
use Plugin\MineAdmin\Tenant\Repository\TenantMenuRepository;

/**
 * @extends IService<Tenant>
 */
final class TenantMenuService extends IService
{
    public function __construct(
        protected readonly TenantMenuRepository $repository,
    ) {}

    public function getRepository(): TenantMenuRepository
    {
        return $this->repository;
    }

    public function getCurrentTenantMenus(): Collection
    {
        return $this->repository->getCurrentTenantMenus();
    }
}
