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
use App\Http\Admin\Request\Game\GameGroupConfigRequest;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Schema\Game\GameGroupConfigSchema;
use App\Service\Game\GameGroupConfigService;
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
use Mine\Swagger\Attributes\ResultResponse;

/**
 * 游戏群组配置表控制器
 * Class GameGroupConfigController
 */
#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
final class GameGroupConfigController extends AdminAbstractController
{

     /**
     * 业务处理服务
     * GameGroupConfigService
     */
    public function __construct(
        protected readonly GameGroupConfigService $service,
        protected readonly CurrentUser $currentUser
    ) {}

    

    #[Get(
        path: '/game/gameGroupConfig/list',
        operationId: 'GameGamegroupconfigList',
        summary: '游戏群组配置表控制器列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['游戏群组配置表控制器'],
    )]
    #[Permission(code: 'game:gameGroupConfig:index')]
    #[PageResponse(instance: GameGroupConfigSchema::class)]
    public function page(Request $request): Result
    {
        return $this->success(data: $this->service->page(array_merge([

        ], $request->all()), (int) $request->query('page'), (int) $request->query('page_size')));
    }


    #[Post(
        path: '/game/gameGroupConfig',
        operationId: 'GameGamegroupconfigCreate',
        summary: '创建游戏群组配置表控制器',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['游戏群组配置表控制器'],
    )]
    #[RequestBody(
        content: new JsonContent(ref: GameGroupConfigRequest::class, title: '创建游戏群组配置表控制器')
    )]
    #[PageResponse(instance: new Result())]
    #[Permission(code: 'game:gameGroupConfig:save')]
    public function create(GameGroupConfigRequest $request): Result
    {
        $this->service->create(array_merge($request->post(), [
            'created_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }


    #[Get('/game/gameGroupConfig/remote')]
    #[ResultResponse(instance: new Result())]
    public function remote(\Mine\Support\Request $request): Result
    {
        $fields = [
            'id',
            'tenant_id',
            'tg_chat_id',
            'tg_chat_title',
        ];
        return $this->success(
            $this->service->getList($request->all())->map(static fn($model) => $model->only($fields))
        );
    }

    #[Put(
        path: '/game/gameGroupConfig/{id}',
        operationId: 'GameGamegroupconfigEdit',
        summary: '编辑游戏群组配置表控制器',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['游戏群组配置表控制器']
    )]
    #[RequestBody(
        content: new JsonContent(ref: GameGroupConfigRequest::class, title: '编辑游戏群组配置表控制器')
    )]
    #[PageResponse(instance: new Result())]
    #[Permission(code: 'game:gameGroupConfig:update')]
    public function save(int $id, GameGroupConfigRequest $request): Result
    {
        $this->service->updateById($id, array_merge($request->post(), [
            'updated_by' => $this->currentUser->id(),
        ]));
        return $this->success();
    }


}