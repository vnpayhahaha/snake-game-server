<?php

use App\Model\Permission\Role;
use App\Model\Permission\User;
use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;
use Plugin\MineAdmin\Tenant\Enums\TenantPackageStatus;
use Plugin\MineAdmin\Tenant\Enums\TenantStatus;
use Plugin\MineAdmin\Tenant\Model\Tenant;
use Plugin\MineAdmin\Tenant\Model\TenantPackage;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 租户表
        Schema::create('tenant', function (Blueprint $table) {
            $table->comment('租户表');
            $table->bigIncrements('id')->comment('租户编号');
            $table->string('name', 32)->unique()->comment('租户名称')->unique();
            $table->unsignedBigInteger('package_id')->comment('套餐编号');
            $table->unsignedBigInteger('user_id')->comment('用户编号，租户管理员');
            $table->unsignedInteger('account_count')->default(100)->comment('账号最大数量');
            $table->string('contact_name', 16)->comment('联系人姓名');
            $table->string('contact_phone', 16)->comment('联系人手机');
            $table->string('bind_domain', 128)->comment('绑定域名')->nullable();
            $table->datetime('expire_at')->comment('过期时间');
            $table->tinyInteger('status')->default(1)->comment('租户状态:1=正常,2=停用');
            $table->authorBy();
            $table->datetimes();
            $table->string('remark', 255)->default('')->comment('备注');
        });

        Tenant::truncate();
        Tenant::create([
            'id' => 1,
            'name' => 'MineAdmin',
            'package_id' => 1,
            'user_id' => 1,
            'account_count' => 100,
            'contact_name' => '迈迈',
            'contact_phone' => '13838888588',
            'expire_at' => date('Y-m-d', strtotime('+1 year', strtotime(date('Y-m-d')))),
            'status' => TenantStatus::Normal,
            'created_by' => 1,
            'remark' => '系统默认租户',
        ]);

        TenantPackage::truncate();
        TenantPackage::create([
            'id' => 1,
            'package_name' => '默认套餐',
            'status' => TenantPackageStatus::Normal,
            'remark' => '默认套餐包含所有菜单',
        ]);

        User::query()->where('id', 1)->update(['tenant_id' => 1]);
        Role::query()->where('id', 1)->update(['tenant_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saas_package');
    }
};
