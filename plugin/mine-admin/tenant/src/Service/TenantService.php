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

use App\Repository\Permission\UserRepository;
use App\Service\IService;
use Plugin\MineAdmin\Tenant\Model\Tenant;
use Plugin\MineAdmin\Tenant\Repository\TenantRepository;
use Plugin\MineAdmin\Tenant\Utils\TenantUtils;

/**
 * @extends IService<Tenant>
 */
final class TenantService extends IService
{
    public function __construct(
        protected readonly TenantRepository $repository,
        protected readonly UserRepository $userRepository,
    ) {}

    public function findTenantInfoByUserId(?int $userId): ?Tenant
    {
        $model = $this->userRepository->getModel();
        $user = $model->fillable(array_merge($model->getFillable(), [TenantUtils::TENANT_ID]))
            ->find($userId, 'tenant_id');
        return $this->repository->findById($user->tenant_id);
    }
}
