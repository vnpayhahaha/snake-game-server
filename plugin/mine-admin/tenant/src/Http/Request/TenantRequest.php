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

#[Schema(title: '租户请求', description: '租户参数', properties: [
    new Property('name', description: '租户名称', type: 'string'),
    new Property('package_id', description: '套餐编码', type: 'string'),
    new Property('account_count', description: '账号最大配额', type: 'string'),
    new Property('contact_name', description: '联系人', type: 'string'),
    new Property('contact_phone', description: '联系人电话', type: 'string'),
    new Property('bind_domain', description: '绑定域名', type: 'string'),
    new Property('status', description: '状态', type: 'string'),
    new Property('expire_at', description: '过期时间', type: 'string'),
    new Property('username', description: '管理员账号', type: 'string'),
    new Property('password', description: '管理员密码', type: 'string'),
    new Property('remark', description: '备注', type: 'string'),
])]
class TenantRequest extends FormRequest
{
    use ClientIpRequestTrait;
    use ClientOsTrait;
    use NoAuthorizeTrait;

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'username' => 'sometimes|string',
            'password' => 'sometimes|string',
            'package_id' => 'required|integer',
            'account_count' => 'required|integer',
            'contact_name' => 'required|string',
            'contact_phone' => 'sometimes|string',
            'expire_at' => 'required|string',
            'remark' => 'sometimes|string',
            'status' => 'sometimes|integer',
            'bind_domain' => 'sometimes|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => trans('tenant.name'),
            'username' => trans('user.username'),
            'password' => trans('user.password'),
            'package_id' => trans('tenant.package_id'),
            'account_count' => trans('tenant.account_count'),
            'contact_name' => trans('tenant.contact_name'),
            'contact_phone' => trans('tenant.contact_phone'),
            'expire_at' => trans('tenant.expire_at'),
            'remark' => trans('tenant.remark'),
            'status' => trans('tenant.status'),
            'bind_domain' => trans('tenant.bind_domain'),
        ];
    }
}
