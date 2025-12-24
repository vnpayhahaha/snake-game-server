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

use App\Http\Admin\Controller\AbstractController as AdminAbstractController;
use App\Http\Admin\Middleware\PermissionMiddleware;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\OperationMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Schema\Prize\PrizeRecordSchema;
use App\Service\Prize\PrizeRecordService;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Request;
use Hyperf\Swagger\Annotation\Get;
use Mine\Access\Attribute\Permission;
use Mine\Swagger\Attributes\PageResponse;
use Hyperf\Swagger\Annotation as OA;
/**
 * 中奖记录表控制器
 * Class PrizeRecordController
 */
#[OA\HyperfServer(name: 'http')]
#[Middleware(middleware: AccessTokenMiddleware::class, priority: 100)]
#[Middleware(middleware: PermissionMiddleware::class, priority: 99)]
#[Middleware(middleware: OperationMiddleware::class, priority: 98)]
final class PrizeRecordController extends AdminAbstractController
{

     /**
     * 业务处理服务
     * PrizeRecordService
     */
    public function __construct(
        protected readonly PrizeRecordService $service,
        protected readonly CurrentUser $currentUser
    ) {}

    

    #[Get(
        path: '/prize/prizeRecord/list',
        operationId: 'PrizePrizerecordList',
        summary: '中奖记录表控制器列表',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['中奖记录表控制器'],
    )]
    #[Permission(code: 'prize:prizeRecord:index')]
    #[PageResponse(instance: PrizeRecordSchema::class)]
    public function page(Request $request): Result
    {
        return $this->success(data: $this->service->page(array_merge([

        ], $request->all()), (int) $request->query('page'), (int) $request->query('page_size')));
    }


}