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

namespace App\Http\Admin\Controller\Prize;

use App\Service\Prize\PrizeDispatchQueueService;
use App\Http\Admin\Request\Prize\PrizeDispatchQueueRequest;
use App\Schema\Prize\PrizeDispatchQueueSchema;
use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use Hyperf\HttpServer\Request;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\Swagger\Annotation\Delete;
use Hyperf\Swagger\Annotation\Get;
use Hyperf\Swagger\Annotation\HyperfServer;
use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Post;
use Hyperf\Swagger\Annotation\Put;
use Hyperf\Swagger\Annotation\RequestBody;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\PageResponse;
use App\Http\Admin\Controller\AbstractController as AdminAbstractController;

/**
 * 奖励发放任务队列表控制器
 * Class PrizeDispatchQueueController
 */
#[HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
final class PrizeDispatchQueueController extends AdminAbstractController
{

     /**
     * 业务处理服务
     * PrizeDispatchQueueService
     */
    public function __construct(
        protected readonly PrizeDispatchQueueService $service,
        protected readonly CurrentUser $currentUser
    ) {}

    

    #[Get(
        path: '/prize/prizeDispatchQueue/list',
        operationId: 'PrizePrizedispatchqueueList',
        summary: '奖励发放任务队列表控制器列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['奖励发放任务队列表控制器'],
    )]
    #[Permission(code: 'prize:prizeDispatchQueue:index')]
    #[PageResponse(instance: PrizeDispatchQueueSchema::class)]
    public function page(Request $request): Result
    {
        return $this->success(data: $this->service->page(array_merge([

        ], $request->all()), (int) $request->query('page'), (int) $request->query('page_size')));
    }


}