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

namespace Plugin\MineAdmin\Tenant\Http\Controller;

use App\Http\Admin\Controller\AbstractController;
use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\ResultResponse;
use OpenApi\Attributes\RequestBody;
use Plugin\MineAdmin\Tenant\Http\Request\TenantPackageRequest;
use Plugin\MineAdmin\Tenant\Service\TenantPackageService;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
final class TenantPackageController extends AbstractController
{
    public function __construct(
        private readonly TenantPackageService $service,
        private readonly CurrentUser $currentUser
    ) {}

    #[Get(
        path: '/admin/plugin/tenant_package/list',
        operationId: 'tenantPackageList',
        summary: '套餐列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['套餐管理']
    )]
    #[Permission(code: 'plugin:mine-admin:tenant-package:list')]
    public function pageList(): Result
    {
        return $this->success(
            $this->service->page(
                $this->getRequestData(),
                $this->getCurrentPage(),
                $this->getPageSize()
            )
        );
    }

    #[Post(
        path: '/admin/plugin/tenant_package',
        operationId: 'tenantPackageCreate',
        summary: '创建套餐',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['套餐管理']
    )]
    #[Permission(code: 'plugin:mine-admin:tenant-package:create')]
    #[RequestBody(content: new JsonContent(ref: TenantPackageRequest::class, title: '创建套餐'))]
    #[ResultResponse(new Result())]
    public function create(TenantPackageRequest $request): Result
    {
        $this->service->create(array_merge($request->validated(), [
            'created_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Delete(
        path: '/admin/plugin/tenant_package',
        operationId: 'tenantPackageDelete',
        summary: '删除套餐',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['套餐管理']
    )]
    #[Permission(code: 'plugin:mine-admin:tenant-package:delete')]
    #[ResultResponse(new Result())]
    public function delete(): Result
    {
        $this->service->deleteById($this->getRequestData());
        return $this->success();
    }

    #[Put(
        path: '/admin/plugin/tenant_package/{tenantId}',
        operationId: 'tenantPackageUpdate',
        summary: '更新套餐',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['套餐管理']
    )]
    #[Permission(code: 'plugin:mine-admin:tenant-package:update')]
    #[RequestBody(content: new JsonContent(ref: TenantPackageRequest::class, title: '更新套餐'))]
    #[ResultResponse(new Result())]
    public function save(int $tenantId, TenantPackageRequest $request): Result
    {
        $this->service->updateById($tenantId, array_merge($request->validated(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }
}
