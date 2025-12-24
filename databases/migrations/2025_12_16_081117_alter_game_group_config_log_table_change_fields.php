<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

/**
 * 游戏群组配置变更记录表字段重构迁移
 *
 * 变更说明：
 * 1. 移除单字段记录方式：field_name, old_value, new_value
 * 2. 新增完整配置快照方式：change_params, old_config, new_config
 * 3. 移除 field_name 索引
 *
 * 设计目的：
 * - 支持一次提交多个字段变更
 * - 保存完整配置快照，便于回溯和对比
 * - 支持配置回滚功能
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('game_group_config_log', function (Blueprint $table) {
            // 1. 先删除旧索引
            $table->dropIndex('idx_field_name');

            // 2. 删除旧字段
            $table->dropColumn(['field_name', 'old_value', 'new_value']);

            // 3. 添加新字段（在 tg_chat_id 之后）
            $table->text('change_params')->nullable()->comment('变更参数（JSON格式，记录本次提交的字段）')->after('tg_chat_id');
            $table->text('old_config')->nullable()->comment('变更前的完整配置（JSON格式）')->after('change_params');
            $table->text('new_config')->nullable()->comment('变更后的完整配置（JSON格式）')->after('old_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_group_config_log', function (Blueprint $table) {
            // 1. 删除新字段
            $table->dropColumn(['change_params', 'old_config', 'new_config']);

            // 2. 恢复旧字段
            $table->string('field_name', 50)->default('')->comment('变更字段名')->after('tg_chat_id');
            $table->text('old_value')->nullable()->comment('变更前的值')->after('field_name');
            $table->text('new_value')->nullable()->comment('变更后的值')->after('old_value');

            // 3. 恢复旧索引
            $table->index('field_name', 'idx_field_name');
        });
    }
};
