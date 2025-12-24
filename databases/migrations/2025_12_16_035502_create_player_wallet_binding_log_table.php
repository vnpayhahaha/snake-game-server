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
        Schema::create('player_wallet_binding_log', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->bigInteger('group_id')->comment('群组ID');
            $table->bigInteger('tg_user_id')->comment('Telegram用户ID');
            $table->string('tg_username', 100)->default('')->comment('Telegram用户名');
            $table->string('tg_first_name', 100)->default('')->comment('Telegram名字');
            $table->string('tg_last_name', 100)->default('')->comment('Telegram姓氏');
            $table->string('old_wallet_address', 34)->default('')->comment('变更前钱包地址（首次绑定为空字符串）');
            $table->string('new_wallet_address', 34)->comment('变更后钱包地址');
            $table->tinyInteger('change_type')->comment('变更类型:1=首次绑定,2=更新绑定');
            $table->dateTime('created_at')->nullable()->comment('变更时间');

            // 索引
            $table->index('group_id', 'idx_group_id');
            $table->index('tg_user_id', 'idx_tg_user_id');
            $table->index(['group_id', 'tg_user_id'], 'idx_group_user');
            $table->index('created_at', 'idx_created_at');

            $table->comment('玩家钱包绑定变更记录表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_wallet_binding_log');
    }
};
