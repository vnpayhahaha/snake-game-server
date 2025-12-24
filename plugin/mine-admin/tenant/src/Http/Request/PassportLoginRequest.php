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

namespace Plugin\MineAdmin\Tenant\Http\Request;

use App\Http\Common\Request\Traits\NoAuthorizeTrait;
use Hyperf\Collection\Arr;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;
use Hyperf\Validation\Request\FormRequest;
use Mine\Support\Request\ClientIpRequestTrait;
use Mine\Support\Request\ClientOsTrait;
use Plugin\MineAdmin\Tenant\Annotation\TenantIgnore;

#[Schema(title: '登录请求', description: '登录请求参数', properties: [
    new Property('tenant', description: '多租户名称', type: 'string'),
    new Property('username', description: '用户名', type: 'string'),
    new Property('password', description: '密码', type: 'string'),
])]
class PassportLoginRequest extends FormRequest
{
    use ClientIpRequestTrait;
    use ClientOsTrait;
    use NoAuthorizeTrait;

    #[TenantIgnore]
    public function rules(): array
    {
        return [
            'tenant' => 'required|string|exists:tenant,name',
            'username' => 'required|string|exists:user,username',
            'password' => 'required|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'tenant' => trans('tenant.name'),
            'username' => trans('user.username'),
            'password' => trans('user.password'),
        ];
    }

    public function ip(): string
    {
        return Arr::first($this->getClientIps(), static fn ($ip) => $ip, '0.0.0.0');
    }
}
