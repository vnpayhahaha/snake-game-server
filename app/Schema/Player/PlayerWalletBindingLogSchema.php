<?php
namespace App\Schema\Player;

use App\Model\Player\PlayerWalletBindingLog;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

/**
 * 玩家钱包绑定变更记录表
 */
#[Schema(title: 'PlayerWalletBindingLogSchema')]
class PlayerWalletBindingLogSchema implements \JsonSerializable
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

    #[Property(property: 'old_wallet_address', title: '变更前钱包地址（首次绑定为空字符串）', type: 'varchar')]
    public string $old_wallet_address;

    #[Property(property: 'new_wallet_address', title: '变更后钱包地址', type: 'varchar')]
    public string $new_wallet_address;

    #[Property(property: 'change_type', title: '变更类型', type: 'tinyint')]
    public string $change_type;

    #[Property(property: 'created_at', title: '变更时间', type: 'datetime')]
    public string $created_at;




    public function __construct(PlayerWalletBindingLog $model)
    {
       $this->id = $model->id;
       $this->group_id = $model->group_id;
       $this->tg_user_id = $model->tg_user_id;
       $this->tg_username = $model->tg_username;
       $this->tg_first_name = $model->tg_first_name;
       $this->tg_last_name = $model->tg_last_name;
       $this->old_wallet_address = $model->old_wallet_address;
       $this->new_wallet_address = $model->new_wallet_address;
       $this->change_type = $model->change_type;
       $this->created_at = $model->created_at;

    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id ,'group_id' => $this->group_id ,'tg_user_id' => $this->tg_user_id ,'tg_username' => $this->tg_username ,'tg_first_name' => $this->tg_first_name ,'tg_last_name' => $this->tg_last_name ,'old_wallet_address' => $this->old_wallet_address ,'new_wallet_address' => $this->new_wallet_address ,'change_type' => $this->change_type ,'created_at' => $this->created_at];
    }
}