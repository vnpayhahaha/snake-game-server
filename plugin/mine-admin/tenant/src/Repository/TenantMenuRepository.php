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

use App\Model\Enums\User\Status;
use App\Model\Permission\Menu;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Query\Builder as QueryBuilder;
use Hyperf\Database\Model\Collection;
use Plugin\MineAdmin\Tenant\Model\TenantPackage;
use Plugin\MineAdmin\Tenant\Utils\TenantUtils;

final class TenantMenuRepository extends IRepository
{
    public function __construct(
        protected readonly Menu $model,
        protected readonly TenantRepository $tenantRepository,
    ) {}

    public function enablePageOrderBy(): bool
    {
        return false;
    }

    public function getTenantManageMenuList(array $params = []): \Hyperf\Collection\Collection
    {
        return $this->perQuery($this->getQuery(), $params)->orderBy('sort')->get();
    }

    public function getCurrentTenantMenus(): ?\Hyperf\Collection\Collection
    {
        $model = $this->tenantRepository->findById(TenantUtils::getTenantId())
            ->package()->with(['menus' => function ($query) {
                return $query->where('parent_id', 0)->with(['children' => function ($q) {
                    $q->where('name', 'not like', 'permission:menu%');
                    $q->where('name', 'not like', 'plugin:mine-admin:tenant%');
                    return $q;
                }])->orderBy('sort')->get();
            }])
            ->first();
        return $model?->menus;
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        $whereInName = static function (Builder $query, array|string $code) {
            $query->whereIn('name', Arr::wrap($code));
        };
        return $query
            ->when(Arr::get($params, 'sortable'), static function (Builder $query, array $sortable) {
                $query->orderBy(key($sortable), current($sortable));
            })
            ->when(Arr::get($params, 'code'), $whereInName)
            ->when(Arr::get($params, 'name'), $whereInName)
            ->when(Arr::get($params, 'children'), static function (Builder $query) {
                $query->with(['children' => function($q) {
                    $q->where('name', 'not like', 'permission:menu%');
                    $q->where('name', 'not like', 'plugin:mine-admin:tenant%');
                    return $q;
                }]);
            })->when(Arr::get($params, 'status'), static function (Builder $query, Status $status) {
                $query->where('status', $status);
            })
            ->when(Arr::has($params, 'parent_id'), static function (Builder $query) use ($params) {
                $query->where('parent_id', Arr::get($params, 'parent_id'));
            });
    }
}
