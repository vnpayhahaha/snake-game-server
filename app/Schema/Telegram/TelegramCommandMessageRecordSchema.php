<?php
namespace App\Schema\Telegram;

use App\Model\Telegram\TelegramCommandMessageRecord;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

/**
 * Telegram命令消息记录表
 */
#[Schema(title: 'TelegramCommandMessageRecordSchema')]
class TelegramCommandMessageRecordSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'bigint')]
    public string $id;

    #[Property(property: 'tg_chat_id', title: 'Telegram群组ID', type: 'bigint')]
    public string $tg_chat_id;

    #[Property(property: 'tg_user_id', title: 'Telegram用户ID', type: 'bigint')]
    public string $tg_user_id;

    #[Property(property: 'tg_username', title: 'Telegram用户名', type: 'varchar')]
    public string $tg_username;

    #[Property(property: 'tg_first_name', title: 'Telegram名字', type: 'varchar')]
    public string $tg_first_name;

    #[Property(property: 'tg_last_name', title: 'Telegram姓氏', type: 'varchar')]
    public string $tg_last_name;

    #[Property(property: 'tg_message_id', title: 'Telegram消息ID', type: 'bigint')]
    public string $tg_message_id;

    #[Property(property: 'command', title: '命令名称（如：/wallet, /snake等）', type: 'varchar')]
    public string $command;

    #[Property(property: 'command_params', title: '命令参数（JSON格式）', type: 'text')]
    public string $command_params;

    #[Property(property: 'request_data', title: '完整请求数据（JSON格式）', type: 'text')]
    public string $request_data;

    #[Property(property: 'status', title: '状态', type: 'tinyint')]
    public string $status;

    #[Property(property: 'response_data', title: '响应数据（JSON格式）', type: 'text')]
    public string $response_data;

    #[Property(property: 'error_message', title: '错误信息', type: 'text')]
    public string $error_message;

    #[Property(property: 'is_admin', title: '是否群管理员', type: 'tinyint')]
    public string $is_admin;

    #[Property(property: 'processed_at', title: '处理完成时间', type: 'datetime')]
    public string $processed_at;

    #[Property(property: 'created_at', title: '创建时间', type: 'datetime')]
    public string $created_at;

    #[Property(property: 'updated_at', title: '更新时间', type: 'datetime')]
    public string $updated_at;




    public function __construct(TelegramCommandMessageRecord $model)
    {
       $this->id = $model->id;
       $this->tg_chat_id = $model->tg_chat_id;
       $this->tg_user_id = $model->tg_user_id;
       $this->tg_username = $model->tg_username;
       $this->tg_first_name = $model->tg_first_name;
       $this->tg_last_name = $model->tg_last_name;
       $this->tg_message_id = $model->tg_message_id;
       $this->command = $model->command;
       $this->command_params = $model->command_params;
       $this->request_data = $model->request_data;
       $this->status = $model->status;
       $this->response_data = $model->response_data;
       $this->error_message = $model->error_message;
       $this->is_admin = $model->is_admin;
       $this->processed_at = $model->processed_at;
       $this->created_at = $model->created_at;
       $this->updated_at = $model->updated_at;

    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id ,'tg_chat_id' => $this->tg_chat_id ,'tg_user_id' => $this->tg_user_id ,'tg_username' => $this->tg_username ,'tg_first_name' => $this->tg_first_name ,'tg_last_name' => $this->tg_last_name ,'tg_message_id' => $this->tg_message_id ,'command' => $this->command ,'command_params' => $this->command_params ,'request_data' => $this->request_data ,'status' => $this->status ,'response_data' => $this->response_data ,'error_message' => $this->error_message ,'is_admin' => $this->is_admin ,'processed_at' => $this->processed_at ,'created_at' => $this->created_at ,'updated_at' => $this->updated_at];
    }
}