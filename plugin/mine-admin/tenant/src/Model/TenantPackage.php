<?php

declare(strict_types=1);

namespace Plugin\MineAdmin\Tenant\Model;

use Hyperf\Database\Model\Relations\BelongsToMany;
use Hyperf\DbConnection\Model\Model;
use Plugin\MineAdmin\Tenant\Enums\TenantPackageStatus;

/**
 * @property int $id 套餐ID
 * @property string $package_name 套餐名称
 * @property TenantPackageStatus $status 状态 (1正常 2停用)
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property string $remark 备注
 */
class TenantPackage extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'tenant_package';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'package_name', 'status', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'status' => TenantPackageStatus::class, 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function menus(): belongsToMany
    {
        return $this->belongsToMany(\App\Model\Permission\Menu::class, 'tenant_package_menu', 'package_id', 'menu_id');
    }
}
