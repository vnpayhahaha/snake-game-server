<?php
namespace App\Schema\Game;

use App\Model\Game\GameGroupConfigLog;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

/**
 * 游戏群组配置变更记录表
 */
#[Schema(title: 'GameGroupConfigLogSchema')]
class GameGroupConfigLogSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'bigint')]
    public string $id;

    #[Property(property: 'config_id', title: '配置表ID', type: 'bigint')]
    public string $config_id;

    #[Property(property: 'tg_chat_id', title: 'Telegram群组ID', type: 'bigint')]
    public string $tg_chat_id;

    #[Property(property: 'change_params', title: '变更参数（JSON格式，记录本次提交的字段）', type: 'text')]
    public string $change_params;

    #[Property(property: 'old_config', title: '变更前的完整配置（JSON格式）', type: 'text')]
    public string $old_config;

    #[Property(property: 'new_config', title: '变更后的完整配置（JSON格式）', type: 'text')]
    public string $new_config;

    #[Property(property: 'operator', title: '操作人', type: 'varchar')]
    public string $operator;

    #[Property(property: 'operator_ip', title: '操作IP', type: 'varchar')]
    public string $operator_ip;

    #[Property(property: 'change_source', title: '变更来源', type: 'tinyint')]
    public string $change_source;

    #[Property(property: 'tg_message_id', title: 'Telegram消息ID（仅TG指令时有值）', type: 'bigint')]
    public string $tg_message_id;

    #[Property(property: 'created_at', title: '变更时间', type: 'datetime')]
    public string $created_at;




    public function __construct(GameGroupConfigLog $model)
    {
       $this->id = $model->id;
       $this->config_id = $model->config_id;
       $this->tg_chat_id = $model->tg_chat_id;
       $this->change_params = $model->change_params;
       $this->old_config = $model->old_config;
       $this->new_config = $model->new_config;
       $this->operator = $model->operator;
       $this->operator_ip = $model->operator_ip;
       $this->change_source = $model->change_source;
       $this->tg_message_id = $model->tg_message_id;
       $this->created_at = $model->created_at;

    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id ,'config_id' => $this->config_id ,'tg_chat_id' => $this->tg_chat_id ,'change_params' => $this->change_params ,'old_config' => $this->old_config ,'new_config' => $this->new_config ,'operator' => $this->operator ,'operator_ip' => $this->operator_ip ,'change_source' => $this->change_source ,'tg_message_id' => $this->tg_message_id ,'created_at' => $this->created_at];
    }
}