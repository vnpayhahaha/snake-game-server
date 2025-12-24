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

use App\Http\CurrentUser;
use App\Model\Enums\User\Type;
use App\Model\Permission\Role;
use App\Repository\IRepository;
use App\Repository\Permission\UserRepository;
use App\Service\Permission\UserService;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Plugin\MineAdmin\Tenant\Annotation\TenantIgnore;
use Plugin\MineAdmin\Tenant\Model\Tenant;

/**
 * @extends IRepository<Tenant>
 */
final class TenantRepository extends IRepository
{
    public function __construct(
        protected readonly Tenant $model,
        protected readonly UserService $userService
    ) {}

    public function create(array $data): mixed
    {
        Db::beginTransaction();
        try {
            // 创建用户
            $user = $this->userService->create([
                'username' => $data['username'],
                'password' => $data['password'],
                'nickname' => $data['contact_name'],
                'phone' => $data['contact_phone'],
                'created_by' => $data['created_by'],
            ]);

            // 检查用户是否创建成功
            if (!$user) {
                throw new \RuntimeException('用户创建失败，请检查用户名是否已存在或数据是否有效');
            }

            // 设置用户ID并创建租户
            $data['user_id'] = $user->id;
            $model = parent::create($data);

            // 检查租户是否创建成功
            if (!$model) {
                throw new \RuntimeException('租户创建失败，请检查租户数据是否有效');
            }

            // 为用户分配角色
            $user->roles()->sync([
                'role_id' => 1,
            ]);

            // 设置用户的租户ID
            $user->tenant_id = $model->id;
            $user->fillable(array_merge($user->getFillable(), ['tenant_id']))->save();
            
            Db::commit();
            return $model;
        } catch (\Exception $e) {
            Db::rollBack();
            throw new \RuntimeException('租户创建失败: ' . $e->getMessage());
        }
    }

    public function findTenant(string $tenant): ?Tenant
    {
        return $this->model->newQuery()
            ->where('name', $tenant)
            ->with('package')
            ->firstOrFail();
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query->with(['user', 'package'])
            ->when(Arr::get($params, 'name'), static function (Builder $query, $name) {
                $query->where('name', 'like', '%' . $name . '%');
            })
            ->when(Arr::get($params, 'status'), static function (Builder $query, $status) {
                $query->where('status', $status);
            })
            ->when(Arr::get($params, 'contact_name'), static function (Builder $query, $contact_name) {
                $query->where('contact_name', $contact_name);
            })
            ->when(Arr::get($params, 'contact_phone'), static function (Builder $query, $contact_phone) {
                $query->where('contact_phone', $contact_phone);
            })
            ->when(Arr::exists($params, 'expire_at'), static function (Builder $query) use ($params) {
                $query->whereBetween('expire_at', [
                    Arr::get($params, 'expire_at')[0] . ' 00:00:00',
                    Arr::get($params, 'expire_at')[1] . ' 23:59:59',
                ]);
            })
            ->when(Arr::exists($params, 'created_at'), static function (Builder $query) use ($params) {
                $query->whereBetween('created_at', [
                    Arr::get($params, 'created_at')[0] . ' 00:00:00',
                    Arr::get($params, 'created_at')[1] . ' 23:59:59',
                ]);
            });
    }
}
