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

namespace Plugin\MineAdmin\Tenant\Service;

use App\Exception\BusinessException;
use App\Exception\JwtInBlackException;
use App\Http\Common\ResultCode;
use App\Model\Enums\User\Type;
use App\Repository\Permission\UserRepository;
use App\Service\IService;
use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\UnencryptedToken;
use Mine\Jwt\Factory;
use Mine\Jwt\JwtInterface;
use Mine\JwtAuth\Event\UserLoginEvent;
use Mine\JwtAuth\Interfaces\CheckTokenInterface;
use Plugin\MineAdmin\Tenant\Annotation\TenantIgnore;
use Plugin\MineAdmin\Tenant\Exception\TenantException;
use Plugin\MineAdmin\Tenant\Repository\TenantRepository;
use Plugin\MineAdmin\Tenant\Utils\ResultCode as TResultCode;
use Plugin\MineAdmin\Tenant\Utils\TenantUtils;
use Psr\EventDispatcher\EventDispatcherInterface;

final class PassportService extends IService implements CheckTokenInterface
{
    /**
     * @var string jwt场景
     */
    private string $jwt = 'default';

    public function __construct(
        private readonly TenantRepository $repository,
        protected readonly UserRepository $userRepository,
        protected readonly Factory $jwtFactory,
        protected readonly EventDispatcherInterface $dispatcher
    ) {}

    /**
     * @return array<string,int|string>
     */
    public function login(array $data, Type $userType = Type::SYSTEM, string $ip = '0.0.0.0', string $browser = 'unknown', string $os = 'unknown'): array
    {
        $tenant = $this->repository->findTenant($data['tenant']);
        $user = $this->userRepository->findByUnameType($data['username'], $userType);

        if ($tenant->status->isDisable()) {
            throw new TenantException(TResultCode::TENANT_DISABLE);
        }
        if ($tenant->package->status->isDisable()) {
            throw new TenantException(TResultCode::TENANT_PACKAGE_DISABLE);
        }
        if (time() > strtotime($tenant->expire_at)) {
            throw new TenantException(TResultCode::PACKAGE_EXPIRE);
        }

        if (empty($user->id)) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, trans('tenant.user_not_found'));
        }
        Context::set(TenantUtils::TENANT_ID, $tenant->id);
        if (! $user->verifyPassword($data['password'])) {
            $this->dispatcher->dispatch(new UserLoginEvent($user, $ip, $os, $browser, false));
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, trans('auth.password_error'));
        }
        if ($user->status->isDisable()) {
            throw new BusinessException(ResultCode::DISABLED);
        }
        $this->dispatcher->dispatch(new UserLoginEvent($user, $ip, $os, $browser));
        $jwt = $this->getJwt();
        return [
            'access_token' => $jwt->builderAccessToken((string) $user->id)->toString(),
            'refresh_token' => $jwt->builderRefreshToken((string) $user->id)->toString(),
            'expire_at' => (int) $jwt->getConfig('ttl', 0),
        ];
    }

    public function checkJwt(UnencryptedToken $token): void
    {
        $this->getJwt()->hasBlackList($token) && throw new JwtInBlackException();
    }

    public function logout(UnencryptedToken $token): void
    {
        $this->getJwt()->addBlackList($token);
    }

    public function getJwt(): JwtInterface
    {
        return $this->jwtFactory->get($this->jwt);
    }

    /**
     * @return array<string,int|string>
     */
    public function refreshToken(UnencryptedToken $token): array
    {
        return value(static function (JwtInterface $jwt) use ($token) {
            $jwt->addBlackList($token);
            return [
                'access_token' => $jwt->builderAccessToken($token->claims()->get(RegisteredClaims::ID))->toString(),
                'refresh_token' => $jwt->builderRefreshToken($token->claims()->get(RegisteredClaims::ID))->toString(),
                'expire_at' => (int) $jwt->getConfig('ttl', 0),
            ];
        }, $this->getJwt());
    }
}
