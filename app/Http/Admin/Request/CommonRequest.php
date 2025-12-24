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

namespace App\Http\Admin\Request;

use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;
use Hyperf\Validation\Request\FormRequest;

/**
 * 公共接口请求验证
 */
#[Schema(
    title: '获取下拉选项',
    properties: [
        new Property(property: 'table_name', description: '表名', type: 'string'),
        new Property(property: 'list_name', description: '列表名', type: 'string'),
    ]
)]
class CommonRequest extends FormRequest
{
    /**
     * 确定用户是否有权限进行此请求
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 获取下拉选项验证规则
     */
    public function rules(): array
    {
        return [
            'table_name' => 'required|string',
            'list_name' => 'required|string',
        ];
    }

    /**
     * 字段名称
     */
    public function attributes(): array
    {
        return [
            'table_name' => '表名',
            'list_name' => '列表名',
        ];
    }
}