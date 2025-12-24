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
namespace App\Http\Admin\Request\Prize;

use Hyperf\Validation\Request\FormRequest;

/**
 * 奖金转账记录表验证数据类
 */
class PrizeTransferRequest extends FormRequest
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
            //中奖节点ID 验证
            'node_id' => 'required',
            //收款地址 验证
            'to_address' => 'required',
            //转账金额 验证
            'amount' => 'required',
            //重试次数 验证
            'retry_count' => 'required',

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
            'prize_record_id' => '中奖记录ID',
            'prize_serial_no' => '开奖流水号',
            'node_id' => '中奖节点ID',
            'to_address' => '收款地址',
            'amount' => '转账金额',
            'status' => '状态',
            'retry_count' => '重试次数',

        ];
    }

public function messages(): array
{
    return [
            'id.required' => '必填主键',
            'prize_record_id.required' => '必填中奖记录ID',
            'prize_serial_no.required' => '必填开奖流水号',
            'node_id.required' => '必填中奖节点ID',
            'to_address.required' => '必填收款地址',
            'amount.required' => '必填转账金额',
            'status.required' => '必填状态',
            'retry_count.required' => '必填重试次数',

    ];
}
}