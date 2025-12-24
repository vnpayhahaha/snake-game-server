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
 * 奖励发放任务队列表验证数据类
 */
class PrizeDispatchQueueRequest extends FormRequest
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
            //优先级(1-10,数字越小优先级越高) 验证
            'priority' => 'required',
            //重试次数 验证
            'retry_count' => 'required',
            //最大重试次数 验证
            'max_retry' => 'required',
            //乐观锁版本号 验证
            'version' => 'required',

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
            'prize_transfer_id' => '转账记录ID',
            'group_id' => '群组ID',
            'prize_serial_no' => '开奖流水号',
            'priority' => '优先级(1-10,数字越小优先级越高)',
            'status' => '状态',
            'retry_count' => '重试次数',
            'max_retry' => '最大重试次数',
            'version' => '乐观锁版本号',

        ];
    }

public function messages(): array
{
    return [
            'id.required' => '必填主键',
            'prize_record_id.required' => '必填中奖记录ID',
            'prize_transfer_id.required' => '必填转账记录ID',
            'group_id.required' => '必填群组ID',
            'prize_serial_no.required' => '必填开奖流水号',
            'priority.required' => '必填优先级(1-10,数字越小优先级越高)',
            'status.required' => '必填状态',
            'retry_count.required' => '必填重试次数',
            'max_retry.required' => '必填最大重试次数',
            'version.required' => '必填乐观锁版本号',

    ];
}
}