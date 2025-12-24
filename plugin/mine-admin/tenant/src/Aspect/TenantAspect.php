<?php

namespace Plugin\MineAdmin\Tenant\Aspect;

use Hyperf\Collection\Arr;
use App\Model\Permission\User;
use Hyperf\Context\Context;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Schema\Schema;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Database\Query\Builder;
use Hyperf\Database\Model\Builder as ModelBuilder;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Plugin\MineAdmin\Tenant\Annotation\TenantIgnore;
use Plugin\MineAdmin\Tenant\Exception\TenantException;
use Plugin\MineAdmin\Tenant\Model\Tenant;
use Plugin\MineAdmin\Tenant\Utils\ResultCode;
use Plugin\MineAdmin\Tenant\Utils\TenantUtils;

#[Aspect]
final class TenantAspect extends AbstractAspect
{
    public const CONTEXT_IGNORE_KEY = 'tenant_ignore_state';
    public const CONTEXT_UPDATE_IGNORE = 'tenant_update_state';

    public array $annotations = [
        TenantIgnore::class,
    ];

    public array $classes = [
        ModelBuilder::class . '::create',
        Builder::class . '::insert',
        Builder::class . '::update',
        Builder::class . '::delete',
        Builder::class . '::runSelect',
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        if (
            isset($proceedingJoinPoint->getAnnotationMetadata()->class[TenantIgnore::class])
            || isset($proceedingJoinPoint->getAnnotationMetadata()->method[TenantIgnore::class])
        ) {
            Context::set(self::CONTEXT_IGNORE_KEY, true);
            /**
             * @var null|TenantIgnore $tenantIgnore
             */
            $tenantIgnore = Arr::get($proceedingJoinPoint->getAnnotationMetadata()->class, TenantIgnore::class);
            if ($tenantIgnore === null) {
                $tenantIgnore = Arr::get($proceedingJoinPoint->getAnnotationMetadata()->method, TenantIgnore::class);
            }

            if ($tenantIgnore) {
                try {
                    if ($tenantIgnore->getOnlyDefaultTenantVisible() && !TenantUtils::isDefaultTenant()) {
                        Context::set(self::CONTEXT_IGNORE_KEY, false);
                    }
                } catch (TenantException $e) {}
            }
            return $proceedingJoinPoint->process();
        }

        /**
         * @var Builder $builder
         */
        $builder = $proceedingJoinPoint->getInstance();
        if (
            (
                $proceedingJoinPoint->className === Builder::class
                || $proceedingJoinPoint->className === ModelBuilder::class
            )
            && !Context::get(self::CONTEXT_IGNORE_KEY)
        ) {
            if ($proceedingJoinPoint->className === ModelBuilder::class) {
                $builder = $builder->getQuery();
            }

            try {
                $id = TenantUtils::getTenantId();
                if (Schema::hasColumn($builder->from, TenantUtils::TENANT_ID) && $id) {
                    switch ($proceedingJoinPoint->methodName) {
                        case 'runSelect':
                        case 'delete':
                        case 'update':
                            if (Context::has(self::CONTEXT_UPDATE_IGNORE)) {
                                Context::destroy(self::CONTEXT_UPDATE_IGNORE);
                            } else {
                                $builder->where(TenantUtils::TENANT_ID, '=', $id);
                            }
                            break;
                        case 'insert':
                        case 'create':
                            if ($builder->from === 'user') {
                                $maxCount = Tenant::query()->where('id', $id)->value('account_count');
                                $userCount = User::query()->where(TenantUtils::TENANT_ID, $id)->count();
                                if ($userCount >= $maxCount) {
                                    throw new TenantException(ResultCode::MAX_ACCOUNT_COUNT);
                                }
                            }
                            Context::set(self::CONTEXT_UPDATE_IGNORE, true);
                            /**
                             * @var Model $model
                             */
                            $model = $proceedingJoinPoint->process();
                            $model->fillable(array_merge($model->getFillable(), [TenantUtils::TENANT_ID]))
                                ->setAttribute(TenantUtils::TENANT_ID, $id)
                                ->save();
                            return $model;
                    }
                    Context::destroy(self::CONTEXT_IGNORE_KEY);
                }
            } catch (TenantException $th) {
                if ($th->getResponse()->code === ResultCode::MAX_ACCOUNT_COUNT) {
                    throw new TenantException(ResultCode::MAX_ACCOUNT_COUNT);
                }
            }
        }
        return $proceedingJoinPoint->process();
    }
}