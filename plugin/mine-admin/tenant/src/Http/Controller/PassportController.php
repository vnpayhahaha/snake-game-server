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

use Plugin\MineAdmin\Tenant\Annotation\TenantIgnore;
use Plugin\MineAdmin\Tenant\Http\Request\PassportLoginRequest;
use App\Http\Admin\Vo\PassportLoginVo;
use App\Http\Common\Controller\AbstractController;
use App\Http\Common\Middleware\AccessTokenMiddleware;
use App\Http\Common\Middleware\RefreshTokenMiddleware;
use App\Http\Common\Result;
use App\Http\CurrentUser;
use App\Model\Enums\User\Type;
use Plugin\MineAdmin\Tenant\Schema\UserSchema;
use Plugin\MineAdmin\Tenant\Service\PassportService;
use Hyperf\Collection\Arr;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Swagger\Annotation as OA;
use Hyperf\Swagger\Annotation\Post;
use Mine\Jwt\Traits\RequestScopedTokenTrait;
use Mine\Swagger\Attributes\ResultResponse;
use Plugin\MineAdmin\Tenant\Service\TenantService;

#[OA\HyperfServer(name: 'http')]
final class PassportController extends AbstractController
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly PassportService $passportService,
        private readonly TenantService $service,
        private readonly CurrentUser $currentUser
    ) {}

    #[Post(
        path: '/admin/plugin/tenant/login',
        operationId: 'tenantPassportLogin',
        summary: 'Tenant 系统登录',
        tags: ['admin:tenant:passport']
    )]
    #[ResultResponse(
        instance: new Result(data: new PassportLoginVo()),
        title: '登录成功',
        description: '登录成功返回对象',
        example: '{"code":200,"message":"成功","data":{"access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MjIwOTQwNTYsIm5iZiI6MTcyMjA5NDAiwiZXhwIjoxNzIyMDk0MzU2fQ.7EKiNHb_ZeLJ1NArDpmK6sdlP7NsDecsTKLSZn_3D7k","refresh_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3MjIwOTQwNTYsIm5iZiI6MTcyMjA5NDAiwiZXhwIjoxNzIyMDk0MzU2fQ.7EKiNHb_ZeLJ1NArDpmK6sdlP7NsDecsTKLSZn_3D7k","expire_at":300}}'
    )]
    #[OA\RequestBody(content: new OA\JsonContent(
        ref: PassportLoginRequest::class,
        title: '登录请求参数',
        required: ['username', 'password', 'tenant'],
        example: '{"username":"admin","password":"123456","tenant":"MineAdmin"}'
    ))]
    #[TenantIgnore]
    public function login(PassportLoginRequest $request): Result
    {
        $browser = $request->header('User-Agent') ?: 'unknown';
        $os = $request->os();
        return $this->success(
            $this->passportService->login(
                [
                    'tenant' => $request->input('tenant'),
                    'username' => $request->input('username'),
                    'password' => $request->input('password'),
                ],
                Type::SYSTEM,
                $request->ip(),
                $browser,
                $os
            )
        );
    }

    #[Post(
        path: '/admin/plugin/tenant/logout',
        operationId: 'tenantPassportLogout',
        summary: 'Tenant 退出',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['admin:tenant:passport']
    )]
    #[ResultResponse(instance: new Result(), example: '{"code":200,"message":"成功","data":[]}')]
    #[Middleware(AccessTokenMiddleware::class)]
    public function logout(RequestInterface $request): Result
    {
        $this->passportService->logout($this->getToken());
        return $this->success();
    }

    #[OA\Get(
        path: '/admin/plugin/tenant/getInfo',
        operationId: 'tenantGetInfo',
        summary: '获取用户信息',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['admin:tenant:passport']
    )]
    #[Middleware(AccessTokenMiddleware::class)]
    #[ResultResponse(
        instance: new Result(data: UserSchema::class),
    )]
    #[TenantIgnore]
    public function getInfo(): Result
    {
        return $this->success(
            Arr::only(
                array_merge(
                    [ 'tenant' => $this->service->findTenantInfoByUserId($this->currentUser->id())?->toArray() ?: [] ],
                    $this->currentUser->user()?->toArray() ?: []
                ),
                ['username', 'nickname', 'avatar', 'signed', 'backend_setting', 'phone', 'email', 'tenant']
            )
        );
    }

    #[Post(
        path: '/admin/plugin/tenant/refresh',
        operationId: 'refresh',
        summary: '刷新token',
        security: [['Bearer' => [], 'ApiKey' => []]],
        tags: ['admin:passport']
    )]
    #[Middleware(RefreshTokenMiddleware::class)]
    #[ResultResponse(
        instance: new Result(data: new PassportLoginVo())
    )]
    public function refresh(CurrentUser $user): Result
    {
        return $this->success($user->refresh());
    }
}
