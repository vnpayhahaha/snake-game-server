<?php
namespace App\Schema\Player;

use App\Model\Player\PlayerWalletBinding;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

/**
 * 玩家钱包绑定表
 */
#[Schema(title: 'PlayerWalletBindingSchema')]
class PlayerWalletBindingSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'bigint')]
    public string $id;

    #[Property(property: 'group_id', title: '群组ID', type: 'bigint')]
    public string $group_id;

    #[Property(property: 'tg_user_id', title: 'Telegram用户ID', type: 'bigint')]
    public string $tg_user_id;

    #[Property(property: 'tg_username', title: 'Telegram用户名', type: 'varchar')]
    public string $tg_username;

    #[Property(property: 'tg_first_name', title: 'Telegram名字', type: 'varchar')]
    public string $tg_first_name;

    #[Property(property: 'tg_last_name', title: 'Telegram姓氏', type: 'varchar')]
    public string $tg_last_name;

    #[Property(property: 'wallet_address', title: '绑定的钱包地址', type: 'varchar')]
    public string $wallet_address;

    #[Property(property: 'bind_at', title: '首次绑定时间', type: 'datetime')]
    public string $bind_at;

    #[Property(property: 'created_at', title: '创建时间', type: 'datetime')]
    public string $created_at;

    #[Property(property: 'updated_at', title: '更新时间', type: 'datetime')]
    public string $updated_at;




    public function __construct(PlayerWalletBinding $model)
    {
       $this->id = $model->id;
       $this->group_id = $model->group_id;
       $this->tg_user_id = $model->tg_user_id;
       $this->tg_username = $model->tg_username;
       $this->tg_first_name = $model->tg_first_name;
       $this->tg_last_name = $model->tg_last_name;
       $this->wallet_address = $model->wallet_address;
       $this->bind_at = $model->bind_at;
       $this->created_at = $model->created_at;
       $this->updated_at = $model->updated_at;

    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id ,'group_id' => $this->group_id ,'tg_user_id' => $this->tg_user_id ,'tg_username' => $this->tg_username ,'tg_first_name' => $this->tg_first_name ,'tg_last_name' => $this->tg_last_name ,'wallet_address' => $this->wallet_address ,'bind_at' => $this->bind_at ,'created_at' => $this->created_at ,'updated_at' => $this->updated_at];
    }
}