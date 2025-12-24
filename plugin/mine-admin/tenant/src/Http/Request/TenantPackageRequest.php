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

#[Schema(title: '租户套餐请求', description: '租户套餐参数', properties: [
    new Property('package_name', description: '套餐名称', type: 'string'),
    new Property('remark', description: '套餐备注', type: 'string'),
    new Property('status', description: '状态', type: 'string'),
    new Property('menus', description: '菜单列表', type: 'string'),
])]
class TenantPackageRequest extends FormRequest
{
    use ClientIpRequestTrait;
    use ClientOsTrait;
    use NoAuthorizeTrait;

    public function rules(): array
    {
        return [
            'package_name' => 'required|string',
            'status' => 'required',
            'remark' => 'sometimes|string',
            'menus' => 'sometimes|array',
        ];
    }

    public function attributes(): array
    {
        return [
            'package_name' => trans('tenant.package.name'),
            'remark' => trans('tenant.common.remark'),
            'status' => trans('tenant.common.status'),
            'menus' => trans('tenant.package.menu'),
        ];
    }
}
