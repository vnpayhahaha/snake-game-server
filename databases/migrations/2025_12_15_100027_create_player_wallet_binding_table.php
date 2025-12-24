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
        Schema::create('player_wallet_binding', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->bigInteger('group_id')->comment('群组ID');
            $table->bigInteger('tg_user_id')->comment('Telegram用户ID');
            $table->string('tg_username', 100)->default('')->comment('Telegram用户名');
            $table->string('tg_first_name', 100)->default('')->comment('Telegram名字');
            $table->string('tg_last_name', 100)->default('')->comment('Telegram姓氏');
            $table->string('wallet_address', 34)->comment('绑定的钱包地址');
            $table->dateTime('bind_at')->nullable()->comment('首次绑定时间');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');

            // 唯一键
            $table->unique(['group_id', 'tg_user_id'], 'uk_group_user');

            // 索引
            $table->index('wallet_address', 'idx_wallet_address');
            $table->index('group_id', 'idx_group_id');
            $table->index('tg_user_id', 'idx_tg_user_id');

            $table->comment('玩家钱包绑定表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_wallet_binding');
    }
};
