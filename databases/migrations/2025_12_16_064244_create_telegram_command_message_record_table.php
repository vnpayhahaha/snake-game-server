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
        Schema::create('telegram_command_message_record', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->bigInteger('tg_chat_id')->comment('Telegram群组ID');
            $table->bigInteger('tg_user_id')->comment('Telegram用户ID');
            $table->string('tg_username', 100)->default('')->comment('Telegram用户名');
            $table->string('tg_first_name', 100)->default('')->comment('Telegram名字');
            $table->string('tg_last_name', 100)->default('')->comment('Telegram姓氏');
            $table->bigInteger('tg_message_id')->comment('Telegram消息ID');
            $table->string('command', 50)->default('')->comment('命令名称（如：/wallet, /snake等）');
            $table->text('command_params')->nullable()->comment('命令参数（JSON格式）');
            $table->text('request_data')->nullable()->comment('完整请求数据（JSON格式）');
            $table->tinyInteger('status')->default(1)->comment('状态:1=待处理,2=处理中,3=成功,4=失败');
            $table->text('response_data')->nullable()->comment('响应数据（JSON格式）');
            $table->text('error_message')->nullable()->comment('错误信息');
            $table->boolean('is_admin')->default(0)->comment('是否群管理员');
            $table->dateTime('processed_at')->nullable()->comment('处理完成时间');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');

            // 索引
            $table->index('tg_chat_id', 'idx_tg_chat_id');
            $table->index('tg_user_id', 'idx_tg_user_id');
            $table->index('tg_message_id', 'idx_tg_message_id');
            $table->index('command', 'idx_command');
            $table->index('status', 'idx_status');
            $table->index(['tg_chat_id', 'command'], 'idx_chat_command');
            $table->index('created_at', 'idx_created_at');

            $table->comment('Telegram命令消息记录表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('telegram_command_message_record');
    }
};
