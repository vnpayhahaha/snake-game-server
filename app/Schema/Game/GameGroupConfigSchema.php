<?php
namespace App\Schema\Game;

use App\Model\Game\GameGroupConfig;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

/**
 * 游戏群组配置表
 */
#[Schema(title: 'GameGroupConfigSchema')]
class GameGroupConfigSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'bigint')]
    public string $id;

    #[Property(property: 'tenant_id', title: '租户ID', type: 'varchar')]
    public string $tenant_id;

    #[Property(property: 'tg_chat_id', title: 'Telegram群组ID', type: 'bigint')]
    public string $tg_chat_id;

    #[Property(property: 'tg_chat_title', title: '群组名称', type: 'varchar')]
    public string $tg_chat_title;

    #[Property(property: 'wallet_address', title: 'TRON钱包地址', type: 'varchar')]
    public string $wallet_address;

    #[Property(property: 'wallet_change_count', title: '钱包变更次数（用于区分不同钱包周期）', type: 'int')]
    public string $wallet_change_count;

    #[Property(property: 'pending_wallet_address', title: '待更新的钱包地址', type: 'varchar')]
    public string $pending_wallet_address;

    #[Property(property: 'wallet_change_status', title: '钱包变更状态', type: 'tinyint')]
    public string $wallet_change_status;

    #[Property(property: 'wallet_change_start_at', title: '钱包变更开始时间', type: 'datetime')]
    public string $wallet_change_start_at;

    #[Property(property: 'wallet_change_end_at', title: '钱包变更生效时间', type: 'datetime')]
    public string $wallet_change_end_at;

    #[Property(property: 'hot_wallet_address', title: '热钱包地址（用于转账）', type: 'varchar')]
    public string $hot_wallet_address;

    #[Property(property: 'hot_wallet_private_key', title: '热钱包私钥（加密存储）', type: 'varchar')]
    public string $hot_wallet_private_key;

    #[Property(property: 'bet_amount', title: '投注金额(TRX)', type: 'decimal')]
    public string $bet_amount;

    #[Property(property: 'platform_fee_rate', title: '平台手续费比例(默认10%)', type: 'decimal')]
    public string $platform_fee_rate;

    #[Property(property: 'status', title: '状态 1-正常 0-停用', type: 'tinyint')]
    public string $status;

    #[Property(property: 'created_at', title: '创建时间', type: 'datetime')]
    public string $created_at;

    #[Property(property: 'updated_at', title: '更新时间', type: 'datetime')]
    public string $updated_at;




    public function __construct(GameGroupConfig $model)
    {
       $this->id = $model->id;
       $this->tenant_id = $model->tenant_id;
       $this->tg_chat_id = $model->tg_chat_id;
       $this->tg_chat_title = $model->tg_chat_title;
       $this->wallet_address = $model->wallet_address;
       $this->wallet_change_count = $model->wallet_change_count;
       $this->pending_wallet_address = $model->pending_wallet_address;
       $this->wallet_change_status = $model->wallet_change_status;
       $this->wallet_change_start_at = $model->wallet_change_start_at;
       $this->wallet_change_end_at = $model->wallet_change_end_at;
       $this->hot_wallet_address = $model->hot_wallet_address;
       $this->hot_wallet_private_key = $model->hot_wallet_private_key;
       $this->bet_amount = $model->bet_amount;
       $this->platform_fee_rate = $model->platform_fee_rate;
       $this->status = $model->status;
       $this->created_at = $model->created_at;
       $this->updated_at = $model->updated_at;

    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id ,'tenant_id' => $this->tenant_id ,'tg_chat_id' => $this->tg_chat_id ,'tg_chat_title' => $this->tg_chat_title ,'wallet_address' => $this->wallet_address ,'wallet_change_count' => $this->wallet_change_count ,'pending_wallet_address' => $this->pending_wallet_address ,'wallet_change_status' => $this->wallet_change_status ,'wallet_change_start_at' => $this->wallet_change_start_at ,'wallet_change_end_at' => $this->wallet_change_end_at ,'hot_wallet_address' => $this->hot_wallet_address ,'hot_wallet_private_key' => $this->hot_wallet_private_key ,'bet_amount' => $this->bet_amount ,'platform_fee_rate' => $this->platform_fee_rate ,'status' => $this->status ,'created_at' => $this->created_at ,'updated_at' => $this->updated_at];
    }
}