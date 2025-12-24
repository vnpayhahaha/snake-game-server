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
        Schema::create('snake_node', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->bigInteger('group_id')->comment('群组ID');
            $table->integer('wallet_cycle')->comment('钱包周期（对应wallet_change_count）');
            $table->char('ticket_number', 2)->comment('购彩凭证(00-99)');
            $table->string('ticket_serial_no', 30)->comment('凭证流水号(格式: YYYYMMDD-序号，如: 20250108-001)');
            $table->string('player_address', 34)->comment('玩家钱包地址');
            $table->string('player_tg_username', 100)->nullable()->comment('Telegram用户名');
            $table->bigInteger('player_tg_user_id')->nullable()->comment('Telegram用户ID');
            $table->decimal('amount', 20, 8)->default(0.00000000)->comment('投注金额');
            $table->string('tx_hash', 64)->unique()->comment('交易哈希');
            $table->bigInteger('block_height')->comment('区块高度');
            $table->integer('daily_sequence')->comment('当天第几笔交易（从1开始）');
            $table->tinyInteger('status')->default(1)->comment('状态:1=活跃,2=已中奖,3=未中奖');
            $table->bigInteger('matched_prize_id')->nullable()->comment('匹配的中奖记录ID');
            $table->dateTime('created_at')->nullable()->comment('创建时间');

            // 索引
            $table->index(['group_id', 'status'], 'idx_group_status');
            $table->index('tx_hash', 'idx_tx_hash');
            $table->index(['player_address', 'group_id'], 'idx_player');
            $table->index('ticket_serial_no', 'idx_serial_no');
            $table->index(['group_id', 'created_at', 'daily_sequence'], 'idx_daily_sequence');
            $table->index(['group_id', 'wallet_cycle', 'status'], 'idx_wallet_cycle');

            $table->comment('蛇身节点记录表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('snake_node');
    }
};
