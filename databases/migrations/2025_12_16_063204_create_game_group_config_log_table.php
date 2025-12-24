<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('game_group_config_log', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->bigInteger('config_id')->comment('配置表ID');
            $table->bigInteger('tg_chat_id')->comment('Telegram群组ID');
            $table->text('change_params')->nullable()->comment('变更参数（JSON格式，记录本次提交的字段）');
            $table->text('old_config')->nullable()->comment('变更前的完整配置（JSON格式）');
            $table->text('new_config')->nullable()->comment('变更后的完整配置（JSON格式）');
            $table->string('operator', 50)->default('')->comment('操作人');
            $table->string('operator_ip', 50)->default('')->comment('操作IP');
            $table->tinyInteger('change_source')->default(1)->comment('变更来源:1=后台编辑,2=TG群指令');
            $table->bigInteger('tg_message_id')->nullable()->comment('Telegram消息ID（仅TG指令时有值）');
            $table->dateTime('created_at')->nullable()->comment('变更时间');

            // 索引
            $table->index('config_id', 'idx_config_id');
            $table->index('tg_chat_id', 'idx_tg_chat_id');
            $table->index('change_source', 'idx_change_source');
            $table->index('created_at', 'idx_created_at');

            $table->comment('游戏群组配置变更记录表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_group_config_log');
    }
};
