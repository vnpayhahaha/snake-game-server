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

namespace Plugin\MineAdmin\Tenant\Repository;

use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Plugin\MineAdmin\Tenant\Model\TenantPackage;
use Plugin\MineAdmin\Tenant\Utils\TenantUtils;
use Psr\Container\ContainerInterface;

/**
 * @extends IRepository<TenantPackage>
 */
final class TenantPackageRepository extends IRepository
{
    public function __construct(
        protected readonly TenantPackage $model,
        protected readonly TenantMenuRepository $repository
    ) {}


    public function create(array $data): mixed
    {
        /**
         * @var TenantPackage $model
         */
        $model = parent::create($data);
        return $this->handleMenus($model, $data['menus'] ?? []);
    }

    public function deleteById(mixed $id): int
    {
        $model = $this->findById($id);
        $this->handleMenus($model, []);
        return parent::deleteById($id);
    }

    public function updateById(mixed $id, array $data): bool
    {
        $result = parent::updateById($id, $data);
        $model = $this->findById($id);
        $this->handleMenus($model, $data['menus'] ?? []);
        return $result;
    }

    protected function handleMenus(TenantPackage $model, array $menus): mixed
    {
        if (count($menus) === 0) {
            $model->menus()->detach();
            return $model;
        }

        $model->menus()->sync(
            $this->repository
                ->list([ 'code' => $menus ])
                ->map(static fn ($item) => $item->id)
                ->toArray()
        );

        return $model;
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query->with(['menus'])
            ->when(Arr::get($params, 'package_name'), static function (Builder $query, $name) {
                $query->where('package_name', 'like', '%' . $name . '%');
            })
            ->when(Arr::get($params, 'status'), static function (Builder $query, $status) {
                $query->where('status', $status);
            });
    }
}
