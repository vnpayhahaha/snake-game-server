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

namespace Plugin\West\SysSettings\Http\Request;

use Hyperf\Validation\Request\FormRequest;
use Mine\Swagger\Attributes\FormRequest as FormRequestAnnotation;
use Plugin\West\SysSettings\Schema\ConfigGroupSchema;

/**
 * 参数配置分组表验证数据类.
 */
#[FormRequestAnnotation(
    schema: ConfigGroupSchema::class,
    title: '创建用户',
    required: [
        'name',
        'code',
    ],  // 必填字段
    only: [
        'name',
        'code',
    ]  // 仅验证这几个字段
)]
class ConfigGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255',
            'icon' => 'nullable|string|max:255',
            'remark' => 'nullable|string|max:500',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => '配置组名称',
            'code' => '配置组标识',
            'icon' => '配置组图标',
            'created_by' => '创建者',
            'remark' => '备注',
        ];
    }
}
