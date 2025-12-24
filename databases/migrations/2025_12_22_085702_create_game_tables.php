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
        // 4.1.1 tenant (租户表)
        Schema::create('tenant', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tenant_id', 20)->unique()->comment('租户编号');
            $table->string('company_name', 200)->comment('租户名称');
            $table->string('contact_phone', 20)->nullable()->comment('联系电话');
            $table->tinyInteger('status')->default(1)->comment('状态 1-正常 0-停用');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index('tenant_id');
        });

        // 4.1.2 game_group (游戏群组表)
        Schema::create('game_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('tenant_id', 20)->comment('租户ID');
            $table->bigInteger('tg_chat_id')->unique()->comment('Telegram群组ID');
            $table->string('tg_chat_title', 255)->comment('群组名称');
            $table->string('wallet_address', 34)->comment('TRON钱包地址');
            $table->integer('wallet_change_count')->default(0)->comment('钱包变更次数（用于区分不同钱包周期）');
            $table->string('pending_wallet_address', 34)->nullable()->comment('待更新的钱包地址');
            $table->enum('wallet_change_status', ['normal', 'changing'])->default('normal')->comment('钱包变更状态');
            $table->timestamp('wallet_change_start_at')->nullable()->comment('钱包变更开始时间');
            $table->timestamp('wallet_change_end_at')->nullable()->comment('钱包变更生效时间');
            $table->string('hot_wallet_address', 34)->nullable()->comment('热钱包地址（用于转账）');
            $table->string('hot_wallet_private_key', 128)->nullable()->comment('热钱包私钥（加密存储）');
            $table->decimal('bet_amount', 10, 4)->default(5.0000)->comment('投注金额(TRX)');
            $table->decimal('platform_fee_rate', 5, 4)->default(0.1000)->comment('平台手续费比例(默认10%)');
            $table->tinyInteger('status')->default(1)->comment('状态 1-正常 0-停用');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index('tenant_id');
            $table->index('tg_chat_id');
            $table->index('wallet_change_status');
        });

        // 4.1.3 snake_node (蛇身节点表)
        Schema::create('snake_node', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('group_id')->comment('群组ID');
            $table->integer('wallet_cycle')->comment('钱包周期（对应wallet_change_count）');
            $table->char('ticket_number', 2)->comment('购彩凭证(00-99)');
            $table->string('ticket_serial_no', 30)->comment('凭证流水号(格式: YYYYMMDD-序号，如: 20250108-001)');
            $table->string('player_address', 34)->comment('玩家钱包地址');
            $table->string('player_tg_username', 100)->nullable()->comment('Telegram用户名');
            $table->bigInteger('player_tg_user_id')->nullable()->comment('Telegram用户ID');
            $table->decimal('amount', 10, 4)->comment('投注金额');
            $table->string('tx_hash', 64)->unique()->comment('交易哈希');
            $table->bigInteger('block_height')->comment('区块高度');
            $table->integer('node_index')->comment('节点序号（在蛇身中的位置）');
            $table->integer('daily_sequence')->comment('当天第几笔交易（从1开始）');
            $table->enum('status', ['active', 'matched', 'cancelled', 'archived'])->default('active')->comment('状态');
            $table->bigInteger('matched_prize_id')->nullable()->comment('匹配的中奖记录ID');
            $table->timestamp('created_at')->nullable();
            $table->index(['group_id', 'status']);
            $table->index('tx_hash');
            $table->index(['player_address', 'group_id']);
            $table->index('ticket_serial_no');
            $table->index(['group_id', 'created_at', 'daily_sequence']);
            $table->index(['group_id', 'wallet_cycle', 'status']);
        });

        // 4.1.4 prize_record (中奖记录表)
        Schema::create('prize_record', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('group_id')->comment('群组ID');
            $table->integer('wallet_cycle')->comment('钱包周期（对应wallet_change_count）');
            $table->char('ticket_number', 2)->comment('中奖凭证');
            $table->bigInteger('start_node_id')->comment('起始节点ID');
            $table->bigInteger('end_node_id')->comment('结束节点ID');
            $table->decimal('total_amount', 10, 4)->comment('区间总金额');
            $table->decimal('platform_fee', 10, 4)->comment('平台抽成');
            $table->decimal('prize_pool', 10, 4)->comment('奖池金额');
            $table->decimal('prize_per_winner', 10, 4)->comment('每人奖金');
            $table->tinyInteger('winner_count')->default(2)->comment('中奖人数');
            $table->enum('status', ['pending', 'transferring', 'completed', 'failed'])->default('pending');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index('group_id');
            $table->index('status');
            $table->index(['group_id', 'wallet_cycle']);
        });

        // 4.1.5 prize_transfer (奖金转账表)
        Schema::create('prize_transfer', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('prize_record_id')->comment('中奖记录ID');
            $table->bigInteger('node_id')->comment('中奖节点ID');
            $table->string('to_address', 34)->comment('收款地址');
            $table->decimal('amount', 10, 4)->comment('转账金额');
            $table->string('tx_hash', 64)->nullable()->comment('转账交易哈希');
            $table->enum('status', ['pending', 'processing', 'success', 'failed'])->default('pending');
            $table->integer('retry_count')->default(0)->comment('重试次数');
            $table->text('error_message')->nullable()->comment('错误信息');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index('prize_record_id');
            $table->index('status');
            $table->index('tx_hash');
        });

        // 4.1.6 tron_transaction_log (TRON交易日志表)
        Schema::create('tron_transaction_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('group_id')->comment('群组ID');
            $table->string('tx_hash', 64)->unique()->comment('交易哈希');
            $table->string('from_address', 34)->comment('发送地址');
            $table->string('to_address', 34)->comment('接收地址');
            $table->decimal('amount', 10, 4)->comment('金额(TRX)');
            $table->bigInteger('block_height')->comment('区块高度');
            $table->bigInteger('block_timestamp')->comment('区块时间戳');
            $table->string('status', 20)->comment('交易状态');
            $table->tinyInteger('is_valid')->default(0)->comment('是否有效交易');
            $table->string('invalid_reason', 255)->nullable()->comment('无效原因');
            $table->tinyInteger('processed')->default(0)->comment('是否已处理');
            $table->timestamp('created_at')->nullable();
            $table->index('group_id');
            $table->index('to_address');
            $table->index('block_height');
            $table->index('processed');
        });

        // 4.1.7 player_wallet_binding (玩家钱包绑定表)
        Schema::create('player_wallet_binding', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('group_id')->comment('群组ID');
            $table->bigInteger('tg_user_id')->comment('Telegram用户ID');
            $table->string('tg_username', 100)->nullable()->comment('Telegram用户名');
            $table->string('tg_first_name', 100)->nullable()->comment('Telegram名字');
            $table->string('tg_last_name', 100)->nullable()->comment('Telegram姓氏');
            $table->string('wallet_address', 34)->comment('绑定的钱包地址');
            $table->tinyInteger('status')->default(1)->comment('状态 1-正常 0-已解绑');
            $table->timestamp('bind_at')->nullable()->comment('绑定时间');
            $table->timestamp('unbind_at')->nullable()->comment('解绑时间');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->unique(['group_id', 'tg_user_id'], 'uk_group_user'); // 复合唯一索引
            $table->index('wallet_address');
            $table->index('group_id');
            $table->index('tg_user_id');
        });

        // 4.1.8 platform_fee_record (平台手续费记录表)
        Schema::create('platform_fee_record', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('group_id')->comment('群组ID');
            $table->bigInteger('prize_record_id')->comment('中奖记录ID');
            $table->decimal('total_amount', 10, 4)->comment('区间总金额');
            $table->decimal('fee_rate', 5, 4)->comment('手续费比例');
            $table->decimal('fee_amount', 10, 4)->comment('手续费金额');
            $table->decimal('prize_pool', 10, 4)->comment('扣除手续费后的奖池');
            $table->enum('fee_status', ['pending', 'collected', 'failed'])->default('pending')->comment('收取状态');
            $table->string('remark', 500)->nullable()->comment('备注');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->index('group_id');
            $table->index('prize_record_id');
            $table->index('created_at');
            $table->index('fee_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant');
        Schema::dropIfExists('game_group');
        Schema::dropIfExists('snake_node');
        Schema::dropIfExists('prize_record');
        Schema::dropIfExists('prize_transfer');
        Schema::dropIfExists('tron_transaction_log');
        Schema::dropIfExists('player_wallet_binding');
        Schema::dropIfExists('platform_fee_record');
    }
};
