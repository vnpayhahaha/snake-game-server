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

namespace App\Repository\Telegram;

use App\Model\Telegram\TelegramCommandMessageRecord;
use Hyperf\Database\Model\Builder;
use App\Repository\IRepository;
use Hyperf\Collection\Arr;

/**
 * Telegram命令消息记录表 Repository类
 */
class TelegramCommandMessageRecordRepository extends IRepository
{
   public function __construct(
        protected readonly TelegramCommandMessageRecord $model
    ) {}

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        
        // 主键
        if (isset($params['id']) && filled($params['id'])) {
            $query->where('id', '=', $params['id']);
        }

        // Telegram群组ID
        if (isset($params['tg_chat_id']) && filled($params['tg_chat_id'])) {
            $query->where('tg_chat_id', '=', $params['tg_chat_id']);
        }

        // Telegram用户ID
        if (isset($params['tg_user_id']) && filled($params['tg_user_id'])) {
            $query->where('tg_user_id', '=', $params['tg_user_id']);
        }

        // Telegram用户名
        if (isset($params['tg_username']) && filled($params['tg_username'])) {
            $query->where('tg_username', 'like', '%'.$params['tg_username'].'%');
        }

        // Telegram名字
        if (isset($params['tg_first_name']) && filled($params['tg_first_name'])) {
            $query->where('tg_first_name', 'like', '%'.$params['tg_first_name'].'%');
        }

        // Telegram姓氏
        if (isset($params['tg_last_name']) && filled($params['tg_last_name'])) {
            $query->where('tg_last_name', 'like', '%'.$params['tg_last_name'].'%');
        }

        // Telegram消息ID
        if (isset($params['tg_message_id']) && filled($params['tg_message_id'])) {
            $query->where('tg_message_id', '=', $params['tg_message_id']);
        }

        // 命令名称（如：/wallet, /snake等）
        if (isset($params['command']) && filled($params['command'])) {
            $query->where('command', 'like', '%'.$params['command'].'%');
        }

        // 命令参数（JSON格式）
        if (isset($params['command_params']) && filled($params['command_params'])) {
            $query->where('command_params', '=', $params['command_params']);
        }

        // 完整请求数据（JSON格式）
        if (isset($params['request_data']) && filled($params['request_data'])) {
            $query->where('request_data', '=', $params['request_data']);
        }

        // 状态
        if (isset($params['status']) && filled($params['status'])) {
            $query->where('status', '=', $params['status']);
        }

        // 响应数据（JSON格式）
        if (isset($params['response_data']) && filled($params['response_data'])) {
            $query->where('response_data', '=', $params['response_data']);
        }

        // 错误信息
        if (isset($params['error_message']) && filled($params['error_message'])) {
            $query->where('error_message', '=', $params['error_message']);
        }

        // 是否群管理员
        if (isset($params['is_admin']) && filled($params['is_admin'])) {
            $query->where('is_admin', '=', $params['is_admin']);
        }

        // 处理完成时间
        if (isset($params['processed_at']) && filled($params['processed_at']) && is_array($params['processed_at']) && count($params['processed_at']) == 2) {
            $query->whereBetween(
                'processed_at',
                [ $params['processed_at'][0], $params['processed_at'][1] ]
            );
        }

        // 创建时间
        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [ $params['created_at'][0], $params['created_at'][1] ]
            );
        }

        // 更新时间
        if (isset($params['updated_at']) && filled($params['updated_at']) && is_array($params['updated_at']) && count($params['updated_at']) == 2) {
            $query->whereBetween(
                'updated_at',
                [ $params['updated_at'][0], $params['updated_at'][1] ]
            );
        }

        return $query;
    }
}