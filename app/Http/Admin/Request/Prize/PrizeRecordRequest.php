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
 * 中奖记录表验证数据类
 */
class PrizeRecordRequest extends FormRequest
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
            //钱包周期（对应wallet_change_count） 验证
            'wallet_cycle' => 'required',
            //中奖凭证 验证
            'ticket_number' => 'required',
            //中奖节点ID（首） 验证
            'winner_node_id_first' => 'required',
            //中奖节点ID（尾） 验证
            'winner_node_id_last' => 'required',
            //中奖区间所有节点ID（逗号分割） 验证
            'winner_node_ids' => 'required',
            //区间总金额 验证
            'total_amount' => 'required',
            //平台抽成 验证
            'platform_fee' => 'required',
            //手续费比例（记录当时费率） 验证
            'fee_rate' => 'required',
            //奖池金额 验证
            'prize_pool' => 'required',
            //派奖金额（奖池-平台抽成） 验证
            'prize_amount' => 'required',
            //每人奖金 验证
            'prize_per_winner' => 'required',
            //奖池剩余金额（扣除本次中奖后余额） 验证
            'pool_remaining' => 'required',
            //中奖人数 验证
            'winner_count' => 'required',
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
            'group_id' => '群组ID',
            'prize_serial_no' => '开奖流水号(格式: WIN+群ID+日期时间)',
            'wallet_cycle' => '钱包周期（对应wallet_change_count）',
            'ticket_number' => '中奖凭证',
            'winner_node_id_first' => '中奖节点ID（首）',
            'winner_node_id_last' => '中奖节点ID（尾）',
            'winner_node_ids' => '中奖区间所有节点ID（逗号分割）',
            'total_amount' => '区间总金额',
            'platform_fee' => '平台抽成',
            'fee_rate' => '手续费比例（记录当时费率）',
            'prize_pool' => '奖池金额',
            'prize_amount' => '派奖金额（奖池-平台抽成）',
            'prize_per_winner' => '每人奖金',
            'pool_remaining' => '奖池剩余金额（扣除本次中奖后余额）',
            'winner_count' => '中奖人数',
            'status' => '状态',
            'version' => '乐观锁版本号',

        ];
    }

public function messages(): array
{
    return [
            'id.required' => '必填主键',
            'group_id.required' => '必填群组ID',
            'prize_serial_no.required' => '必填开奖流水号(格式: WIN+群ID+日期时间)',
            'wallet_cycle.required' => '必填钱包周期（对应wallet_change_count）',
            'ticket_number.required' => '必填中奖凭证',
            'winner_node_id_first.required' => '必填中奖节点ID（首）',
            'winner_node_id_last.required' => '必填中奖节点ID（尾）',
            'winner_node_ids.required' => '必填中奖区间所有节点ID（逗号分割）',
            'total_amount.required' => '必填区间总金额',
            'platform_fee.required' => '必填平台抽成',
            'fee_rate.required' => '必填手续费比例（记录当时费率）',
            'prize_pool.required' => '必填奖池金额',
            'prize_amount.required' => '必填派奖金额（奖池-平台抽成）',
            'prize_per_winner.required' => '必填每人奖金',
            'pool_remaining.required' => '必填奖池剩余金额（扣除本次中奖后余额）',
            'winner_count.required' => '必填中奖人数',
            'status.required' => '必填状态',
            'version.required' => '必填乐观锁版本号',

    ];
}
}