<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        echo '开始创建 system_setting_config_group 数据表' . \PHP_EOL;
        if (! Schema::hasTable('system_setting_config_group')) {
            Schema::create('system_setting_config_group', static function (Blueprint $table) {
                $table->bigIncrements('id')->comment('主键');
                $table->string('name', 32)->comment('配置组名称');
                $table->string('code', 64)->comment('配置组标识');
                $table->string('icon', 128)->nullable()->comment('配置组图标');
                $table->bigInteger('created_by')->nullable()->comment('创建者');
                $table->bigInteger('updated_by')->nullable()->comment('更新者');
                $table->timestamps(0); // created_at 和 updated_at
                $table->string('remark', 255)->nullable()->comment('备注');
            });
        } else {
            echo '数据表 system_setting_config_group 已存在，跳过创建' . \PHP_EOL;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_setting_config_group');
    }
};
