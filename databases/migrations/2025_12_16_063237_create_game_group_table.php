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
        Schema::create('game_group', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->bigInteger('config_id')->comment('配置表ID');
            $table->bigInteger('tg_chat_id')->unique()->comment('Telegram群组ID');
            $table->decimal('prize_pool_amount', 20, 8)->default(0.00000000)->comment('当前奖池金额');
            $table->text('current_snake_nodes')->nullable()->comment('当前蛇身节点ID（逗号分割）');
            $table->text('last_snake_nodes')->nullable()->comment('上次蛇身节点ID（逗号分割）');
            $table->text('last_prize_nodes')->nullable()->comment('上次中奖区间节点ID（逗号分割）');
            $table->decimal('last_prize_amount', 20, 8)->default(0.00000000)->comment('上次中奖金额');
            $table->string('last_prize_address', 500)->default('')->comment('上次中奖地址（多个用逗号分割）');
            $table->string('last_prize_serial_no', 30)->default('')->comment('上次开奖流水号');
            $table->dateTime('last_prize_at')->nullable()->comment('上次中奖时间');
            $table->integer('version')->default(0)->comment('乐观锁版本号');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');

            // 索引
            $table->index('config_id', 'idx_config_id');
            $table->index('last_prize_serial_no', 'idx_last_prize_serial');

            $table->comment('游戏群组实时状态表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_group');
    }
};
