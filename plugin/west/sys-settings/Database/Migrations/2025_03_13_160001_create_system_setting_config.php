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
        echo '开始创建 system_setting_config 数据表' . \PHP_EOL;
        // 判断表是否存在，避免重复创建
        if (! Schema::hasTable('system_setting_config')) {
            Schema::create('system_setting_config', static function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->bigInteger('group_id')->default(0)->comment('组id');
                $table->string('key', 32)->unique()->comment('配置键名');
                $table->string('value', 255)->nullable()->comment('配置值');
                $table->string('name', 255)->nullable()->comment('配置名称');
                $table->string('input_type', 32)->nullable()->comment('数据输入类型');
                $table->json('config_select_data')->nullable()->comment('配置选项数据');
                $table->smallInteger('sort', false, true)->default(0)->comment('排序');
                $table->string('remark', 255)->nullable()->comment('备注');
                $table->bigInteger('created_by')->nullable()->comment('创建者');
                $table->bigInteger('updated_by')->nullable()->comment('更新者');
                $table->timestamps(0); // created_at 和 updated_at
            });
        } else {
            echo '数据表 system_setting_config 已存在，跳过创建' . \PHP_EOL;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_setting_config');
    }
};
