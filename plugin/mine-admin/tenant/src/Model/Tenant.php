<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\Tenant\Model;

use App\Model\Permission\User;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\DbConnection\Model\Model;
use Plugin\MineAdmin\Tenant\Enums\TenantStatus;

/**
 * @property int $id 租户编号
 * @property string $name 租户名称
 * @property int $package_id 套餐编号
 * @property int $user_id 用户编号，租户管理员
 * @property int $account_count 账号最大数量
 * @property string $contact_name 联系人姓名
 * @property string $contact_phone 联系人手机
 * @property string $bind_domain 绑定域名
 * @property string $expire_at 过期时间
 * @property TenantStatus $status 状态 (1正常 2停用)
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $remark 备注
 * @property User $user 用户
 * @property TenantPackage $package 用户
 */
class Tenant extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'tenant';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'name', 'package_id', 'user_id', 'account_count', 'contact_name', 'contact_phone', 'bind_domain', 'expire_at', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'package_id' => 'integer',
        'user_id' => 'integer',
        'account_count' => 'integer',
        'status' => TenantStatus::class,
        'created_by' => 'integer',
        'updated_by' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user(): HasOne
    {
        return $this->hasOne(\App\Model\Permission\User::class, 'id', 'user_id');
    }

    public function package(): HasOne
    {
        return $this->hasOne(TenantPackage::class, 'id', 'package_id');
    }
}
