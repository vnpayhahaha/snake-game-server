<?php
namespace App\Schema\Game;

use App\Model\Game\GameGroup;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

/**
 * 游戏群组实时状态表
 */
#[Schema(title: 'GameGroupSchema')]
class GameGroupSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'bigint')]
    public string $id;

    #[Property(property: 'config_id', title: '配置表ID', type: 'bigint')]
    public string $config_id;

    #[Property(property: 'tg_chat_id', title: 'Telegram群组ID', type: 'bigint')]
    public string $tg_chat_id;

    #[Property(property: 'prize_pool_amount', title: '当前奖池金额', type: 'decimal')]
    public string $prize_pool_amount;

    #[Property(property: 'current_snake_nodes', title: '当前蛇身节点ID（逗号分割）', type: 'text')]
    public string $current_snake_nodes;

    #[Property(property: 'last_snake_nodes', title: '上次蛇身节点ID（逗号分割）', type: 'text')]
    public string $last_snake_nodes;

    #[Property(property: 'last_prize_nodes', title: '上次中奖区间节点ID（逗号分割）', type: 'text')]
    public string $last_prize_nodes;

    #[Property(property: 'last_prize_amount', title: '上次中奖金额', type: 'decimal')]
    public string $last_prize_amount;

    #[Property(property: 'last_prize_address', title: '上次中奖地址（多个用逗号分割）', type: 'varchar')]
    public string $last_prize_address;

    #[Property(property: 'last_prize_serial_no', title: '上次开奖流水号', type: 'varchar')]
    public string $last_prize_serial_no;

    #[Property(property: 'last_prize_at', title: '上次中奖时间', type: 'datetime')]
    public string $last_prize_at;

    #[Property(property: 'version', title: '乐观锁版本号', type: 'int')]
    public string $version;

    #[Property(property: 'created_at', title: '创建时间', type: 'datetime')]
    public string $created_at;

    #[Property(property: 'updated_at', title: '更新时间', type: 'datetime')]
    public string $updated_at;




    public function __construct(GameGroup $model)
    {
       $this->id = $model->id;
       $this->config_id = $model->config_id;
       $this->tg_chat_id = $model->tg_chat_id;
       $this->prize_pool_amount = $model->prize_pool_amount;
       $this->current_snake_nodes = $model->current_snake_nodes;
       $this->last_snake_nodes = $model->last_snake_nodes;
       $this->last_prize_nodes = $model->last_prize_nodes;
       $this->last_prize_amount = $model->last_prize_amount;
       $this->last_prize_address = $model->last_prize_address;
       $this->last_prize_serial_no = $model->last_prize_serial_no;
       $this->last_prize_at = $model->last_prize_at;
       $this->version = $model->version;
       $this->created_at = $model->created_at;
       $this->updated_at = $model->updated_at;

    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id ,'config_id' => $this->config_id ,'tg_chat_id' => $this->tg_chat_id ,'prize_pool_amount' => $this->prize_pool_amount ,'current_snake_nodes' => $this->current_snake_nodes ,'last_snake_nodes' => $this->last_snake_nodes ,'last_prize_nodes' => $this->last_prize_nodes ,'last_prize_amount' => $this->last_prize_amount ,'last_prize_address' => $this->last_prize_address ,'last_prize_serial_no' => $this->last_prize_serial_no ,'last_prize_at' => $this->last_prize_at ,'version' => $this->version ,'created_at' => $this->created_at ,'updated_at' => $this->updated_at];
    }
}