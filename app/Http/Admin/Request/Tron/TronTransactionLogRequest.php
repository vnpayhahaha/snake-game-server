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
namespace App\Http\Admin\Request\Tron;

use Hyperf\Validation\Request\FormRequest;

/**
 * TRON交易监听日志表验证数据类
 */
class TronTransactionLogRequest extends FormRequest
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
            //发送地址 验证
            'from_address' => 'required',
            //金额(TRX) 验证
            'amount' => 'required',
            //交易类型 验证
            'transaction_type' => 'required',
            //区块时间戳 验证
            'block_timestamp' => 'required',
            //交易状态 验证
            'status' => 'required',
            //是否有效交易 验证
            'is_valid' => 'required',

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
            'tx_hash' => '交易哈希',
            'from_address' => '发送地址',
            'to_address' => '接收地址',
            'amount' => '金额(TRX)',
            'transaction_type' => '交易类型',
            'block_height' => '区块高度',
            'block_timestamp' => '区块时间戳',
            'status' => '交易状态',
            'is_valid' => '是否有效交易',
            'processed' => '是否已处理',

        ];
    }

public function messages(): array
{
    return [
            'id.required' => '必填主键',
            'group_id.required' => '必填群组ID',
            'tx_hash.required' => '必填交易哈希',
            'from_address.required' => '必填发送地址',
            'to_address.required' => '必填接收地址',
            'amount.required' => '必填金额(TRX)',
            'transaction_type.required' => '必填交易类型',
            'block_height.required' => '必填区块高度',
            'block_timestamp.required' => '必填区块时间戳',
            'status.required' => '必填交易状态',
            'is_valid.required' => '必填是否有效交易',
            'processed.required' => '必填是否已处理',

    ];
}
}