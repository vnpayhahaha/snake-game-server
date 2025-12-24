<?php

namespace Plugin\MineAdmin\Tenant\Utils;

use App\Model\Permission\User;
use Hyperf\Context\Context;
use App\Http\CurrentUser;
use Hyperf\Context\ApplicationContext;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Coroutine\Coroutine;
use Plugin\MineAdmin\Tenant\Aspect\TenantAspect;
use Plugin\MineAdmin\Tenant\Exception\TenantException;
use Plugin\MineAdmin\Tenant\Model\Tenant;

final class TenantUtils
{
    public const TENANT_ID = 'tenant_id';

    static function request(): RequestInterface
    {
        return ApplicationContext::getContainer()->get(RequestInterface::class);
    }

    static function getCurrentUser(): CurrentUser
    {
        return ApplicationContext::getContainer()->get(CurrentUser::class);
    }

    static function getRootCoroutineId(): int
    {
        $id = Coroutine::id();
        while ($id > 0 && $pid = Coroutine::pid($id)) {
            if ($pid === -1) {
                break;
            }
            $id = $pid;
        }
        return $id;
    }

    static function isDefaultTenant(): bool
    {
        return self::getTenantId() === 1;
    }

    static function isTenantManage(): bool
    {
        return self::getCurrentUser()->id() === Tenant::query()->where('id', self::getTenantId())->value('user_id');
    }

    static function getTenantId(): ?int
    {
        try {
            $rootId = self::getRootCoroutineId();
            if (Context::has(self::TENANT_ID)) {
                return Context::get(self::TENANT_ID);
            } else if (Context::has(self::TENANT_ID, $rootId)) {
                return Context::get(self::TENANT_ID, null, $rootId);
            }
            if ($id = self::request()?->getHeaderLine(self::TENANT_ID) ?? null) {
                $id = (int) $id;
                Context::set(self::TENANT_ID, $id);
            } else {
                // 设置忽略标志以避免无限递归
                Context::set(TenantAspect::CONTEXT_IGNORE_KEY, true);
                try {
                    $user = new User;
                    $id = (int) $user->fillable(array_merge($user->getFillable(), [self::TENANT_ID]))
                        ->where('id', self::getCurrentUser()->id())->value(self::TENANT_ID);
                    if ($id) {
                        Context::set(self::TENANT_ID, $id);
                    } else {
                        throw new \Exception;
                    }
                } finally {
                    // 清理忽略标志
                    Context::destroy(TenantAspect::CONTEXT_IGNORE_KEY);
                }
            }
            return $id;
        } catch (\Throwable $th) {
            throw new TenantException(ResultCode::MISS_TENANT_ID);
        }
    }
}