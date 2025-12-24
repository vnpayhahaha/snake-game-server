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
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Model\Enums\User\Status;
use App\Repository\Permission\MenuRepository;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Mine\Swagger\Attributes\ResultResponse;
use Plugin\MineAdmin\Tenant\Annotation\TenantIgnore;
use Plugin\MineAdmin\Tenant\Service\TenantMenuService;
use Plugin\MineAdmin\Tenant\Utils\TenantUtils;

#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
final class TenantMenuController extends AbstractController
{
    public function __construct(
        private readonly TenantMenuService $service,
        private readonly CurrentUser $currentUser,
        private readonly MenuRepository $menuRepository,
    ) {}

    #[Get(
        path: '/admin/plugin/tenant/menu/list',
        operationId: 'menuList',
        summary: '套餐里可配置菜单列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['租户套餐']
    )]
    #[ResultResponse(instance: new Result())]
    public function pageList(RequestInterface $request): Result
    {
        return $this->success(data: $this->service->getRepository()->getTenantManageMenuList([
            'children' => true,
            'parent_id' => 0,
        ]));
    }

    #[Get(
        path: '/admin/plugin/tenant/menus',
        operationId: 'menuList',
        summary: '获取租户可管理的菜单',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['用户信息']
    )]
    #[ResultResponse(instance: new Result())]
    #[TenantIgnore]
    public function menus(RequestInterface $request): Result
    {
        if (TenantUtils::isDefaultTenant()) {
            return $this->success(
                data: $this->currentUser->isSuperAdmin()
                    ? $this->menuRepository->list([
                        'status' => Status::Normal,
                        'children' => true,
                        'parent_id' => 0,
                    ])
                    : $this->currentUser->filterCurrentUser()
            );
        } else {
            return $this->success(data:
                TenantUtils::isTenantManage()
                ? $this->service->getCurrentTenantMenus()
                : $this->currentUser->filterCurrentUser()
            );
        }
    }
}
