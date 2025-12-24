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
namespace App\Http\Admin\Request\Game;

use Hyperf\Validation\Request\FormRequest;

/**
 * 游戏群组配置表验证数据类
 */
class GameGroupConfigRequest extends FormRequest
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
            //群组名称 验证
            'tg_chat_title' => 'required',
            //TRON钱包地址 验证
            'wallet_address' => 'required',
            //钱包变更次数（用于区分不同钱包周期） 验证
            'wallet_change_count' => 'required',
            //钱包变更状态 验证
            'wallet_change_status' => 'required',
            //投注金额(TRX) 验证
            'bet_amount' => 'required',
            //平台手续费比例(默认10%) 验证
            'platform_fee_rate' => 'required',
            //状态 1-正常 0-停用 验证
            'status' => 'required',

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
            'tenant_id' => '租户ID',
            'tg_chat_id' => 'Telegram群组ID',
            'tg_chat_title' => '群组名称',
            'wallet_address' => 'TRON钱包地址',
            'wallet_change_count' => '钱包变更次数（用于区分不同钱包周期）',
            'wallet_change_status' => '钱包变更状态',
            'bet_amount' => '投注金额(TRX)',
            'platform_fee_rate' => '平台手续费比例(默认10%)',
            'status' => '状态 1-正常 0-停用',

        ];
    }

public function messages(): array
{
    return [
            'id.required' => '必填主键',
            'tenant_id.required' => '必填租户ID',
            'tg_chat_id.required' => '必填Telegram群组ID',
            'tg_chat_title.required' => '必填群组名称',
            'wallet_address.required' => '必填TRON钱包地址',
            'wallet_change_count.required' => '必填钱包变更次数（用于区分不同钱包周期）',
            'wallet_change_status.required' => '必填钱包变更状态',
            'bet_amount.required' => '必填投注金额(TRX)',
            'platform_fee_rate.required' => '必填平台手续费比例(默认10%)',
            'status.required' => '必填状态 1-正常 0-停用',

    ];
}
}