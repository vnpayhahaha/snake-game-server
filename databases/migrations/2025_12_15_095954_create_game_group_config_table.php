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
        Schema::create('game_group_config', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->string('tenant_id', 20)->comment('租户ID')->index();
            $table->bigInteger('tg_chat_id')->unique()->comment('Telegram群组ID');
            $table->string('tg_chat_title', 255)->comment('群组名称');
            $table->string('wallet_address', 34)->comment('TRON钱包地址');
            $table->integer('wallet_change_count')->default(0)->comment('钱包变更次数（用于区分不同钱包周期）');
            $table->string('pending_wallet_address', 34)->nullable()->comment('待更新的钱包地址');
            $table->tinyInteger('wallet_change_status')->default(1)->comment('钱包变更状态:1=正常,2=变更中');
            $table->dateTime('wallet_change_start_at')->nullable()->comment('钱包变更开始时间');
            $table->dateTime('wallet_change_end_at')->nullable()->comment('钱包变更生效时间');
            $table->string('hot_wallet_address', 34)->nullable()->comment('热钱包地址（用于转账）');
            $table->string('hot_wallet_private_key', 128)->nullable()->comment('热钱包私钥（加密存储）');
            $table->decimal('bet_amount', 20, 8)->default(5.00000000)->comment('投注金额(TRX)');
            $table->decimal('platform_fee_rate', 5, 4)->default(0.1000)->comment('平台手续费比例(默认10%)');
            $table->boolean('status')->default(1)->comment('状态 1-正常 0-停用');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');

            $table->comment('游戏群组配置表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_group_config');
    }
};
