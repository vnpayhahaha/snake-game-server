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
use Plugin\MineAdmin\Tenant\Annotation\TenantIgnore;
use Plugin\MineAdmin\Tenant\Http\Request\TenantRequest;
use Plugin\MineAdmin\Tenant\Service\TenantService;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
final class TenantController extends AbstractController
{
    public function __construct(
        private readonly TenantService $service,
        private readonly CurrentUser $currentUser
    ) {}

    #[Get(
        path: '/admin/plugin/tenant/list',
        operationId: 'tenantList',
        summary: '租户列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['租户管理']
    )]
    #[Permission(code: 'plugin:mine-admin:tenant:list')]
    #[TenantIgnore]
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
        path: '/admin/plugin/tenant',
        operationId: 'tenantCreate',
        summary: '创建租户',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['租户管理']
    )]
    #[Permission(code: 'plugin:mine-admin:tenant:create')]
    #[RequestBody(content: new JsonContent(ref: TenantRequest::class, title: '创建租户'))]
    #[ResultResponse(new Result())]
    #[TenantIgnore]
    public function create(TenantRequest $request): Result
    {
        $this->service->create(array_merge($request->validated(), [
            'created_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Delete(
        path: '/admin/plugin/tenant',
        operationId: 'tenantDelete',
        summary: '删除租户',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['租户管理']
    )]
    #[Permission(code: 'plugin:mine-admin:tenant:delete')]
    #[ResultResponse(new Result())]
    #[TenantIgnore]
    public function delete(): Result
    {
        $this->service->deleteById($this->getRequestData());
        return $this->success();
    }

    #[Put(
        path: '/admin/plugin/tenant/{tenantId}',
        operationId: 'tenantUpdate',
        summary: '更新租户',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['租户管理']
    )]
    #[Permission(code: 'plugin:mine-admin:tenant:update')]
    #[RequestBody(content: new JsonContent(ref: TenantRequest::class, title: '更新租户'))]
    #[ResultResponse(new Result())]
    #[TenantIgnore]
    public function save(int $tenantId, TenantRequest $request): Result
    {
        $this->service->updateById($tenantId, array_merge($request->validated(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }
}
