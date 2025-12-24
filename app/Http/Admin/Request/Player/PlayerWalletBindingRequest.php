<?php
declare(strict_types=1);
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */
namespace App\Http\Admin\Request\Player;

use Hyperf\Validation\Request\FormRequest;

/**
 * 玩家钱包绑定表验证数据类
 */
class PlayerWalletBindingRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    /**
     * 新增数据验证规则
     * return array
     */
    public function rules(): array
    {
        return [
            //Telegram用户名 验证
            'tg_username' => 'required',
            //Telegram名字 验证
            'tg_first_name' => 'required',
            //Telegram姓氏 验证
            'tg_last_name' => 'required',

        ];
    }


    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '主键',
            'group_id' => '群组ID',
            'tg_user_id' => 'Telegram用户ID',
            'tg_username' => 'Telegram用户名',
            'tg_first_name' => 'Telegram名字',
            'tg_last_name' => 'Telegram姓氏',
            'wallet_address' => '绑定的钱包地址',

        ];
    }

public function messages(): array
{
    return [
            'id.required' => '必填主键',
            'group_id.required' => '必填群组ID',
            'tg_user_id.required' => '必填Telegram用户ID',
            'tg_username.required' => '必填Telegram用户名',
            'tg_first_name.required' => '必填Telegram名字',
            'tg_last_name.required' => '必填Telegram姓氏',
            'wallet_address.required' => '必填绑定的钱包地址',

    ];
}
}