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

class ConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id' => 'required',
            'key' => 'required',
            'name' => 'required',
        ];
    }

    public function attributes(): array
    {
        return [
            'group_id' => '组ID',
            'key' => '配置键名',
            'name' => '配置名称',
        ];
    }
}
