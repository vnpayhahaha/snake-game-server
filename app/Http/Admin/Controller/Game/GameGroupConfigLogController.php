<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace App\Http\Admin\Controller\Game;

use App\Http\Admin\Controller\AbstractController as AdminAbstractController;
use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Admin\Request\Game\GameGroupConfigLogRequest;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Schema\Game\GameGroupConfigLogSchema;
use App\Service\Game\GameGroupConfigLogService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Request;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\RequestBody;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\PageResponse;

/**
 * 游戏群组配置变更记录表控制器
 * Class GameGroupConfigLogController
 */
#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
final class GameGroupConfigLogController extends AdminAbstractController
{

     /**
     * 业务处理服务
     * GameGroupConfigLogService
     */
    public function __construct(
        protected readonly GameGroupConfigLogService $service,
        protected readonly CurrentUser $currentUser
    ) {}

    

    #[Get(
        path: '/game/gameGroupConfigLog/list',
        operationId: 'GameGamegroupconfiglogList',
        summary: '游戏群组配置变更记录表控制器列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['游戏群组配置变更记录表控制器'],
    )]
    #[Permission(code: 'game:gameGroupConfigLog:index')]
    #[PageResponse(instance: GameGroupConfigLogSchema::class)]
    public function page(Request $request): Result
    {
        return $this->success(data: $this->service->page(array_merge([

        ], $request->all()), (int) $request->query('page'), (int) $request->query('page_size')));
    }


    #[Post(
        path: '/game/gameGroupConfigLog',
        operationId: 'GameGamegroupconfiglogCreate',
        summary: '创建游戏群组配置变更记录表控制器',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['游戏群组配置变更记录表控制器'],
    )]
    #[RequestBody(
        content: new JsonContent(ref: GameGroupConfigLogRequest::class, title: '创建游戏群组配置变更记录表控制器')
    )]
    #[PageResponse(instance: new Result())]
    #[Permission(code: 'game:gameGroupConfigLog:save')]
    public function create(GameGroupConfigLogRequest $request): Result
    {
        $this->service->create(array_merge($request->post(), [
            'created_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

    #[Put(
        path: '/game/gameGroupConfigLog/{id}',
        operationId: 'GameGamegroupconfiglogEdit',
        summary: '编辑游戏群组配置变更记录表控制器',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['游戏群组配置变更记录表控制器']
    )]
    #[RequestBody(
        content: new JsonContent(ref: GameGroupConfigLogRequest::class, title: '编辑游戏群组配置变更记录表控制器')
    )]
    #[PageResponse(instance: new Result())]
    #[Permission(code: 'game:gameGroupConfigLog:update')]
    public function save(int $id, GameGroupConfigLogRequest $request): Result
    {
        $this->service->updateById($id, array_merge($request->post(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }

}