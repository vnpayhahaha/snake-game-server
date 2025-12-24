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
        Schema::create('prize_record', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->bigInteger('group_id')->comment('群组ID');
            $table->string('prize_serial_no', 30)->default('')->comment('开奖流水号(格式: WIN+群ID+日期时间)');
            $table->integer('wallet_cycle')->comment('钱包周期（对应wallet_change_count）');
            $table->char('ticket_number', 2)->comment('中奖凭证');
            $table->bigInteger('winner_node_id_first')->comment('中奖节点ID（首）');
            $table->bigInteger('winner_node_id_last')->comment('中奖节点ID（尾）');
            $table->text('winner_node_ids')->comment('中奖区间所有节点ID（逗号分割）');
            $table->decimal('total_amount', 20, 8)->default(0.00000000)->comment('区间总金额');
            $table->decimal('platform_fee', 20, 8)->default(0.00000000)->comment('平台抽成');
            $table->decimal('fee_rate', 5, 4)->comment('手续费比例（记录当时费率）');
            $table->decimal('prize_pool', 20, 8)->default(0.00000000)->comment('奖池金额');
            $table->decimal('prize_amount', 20, 8)->default(0.00000000)->comment('派奖金额（奖池-平台抽成）');
            $table->decimal('prize_per_winner', 20, 8)->default(0.00000000)->comment('每人奖金');
            $table->decimal('pool_remaining', 20, 8)->default(0.00000000)->comment('奖池剩余金额（扣除本次中奖后余额）');
            $table->tinyInteger('winner_count')->default(2)->comment('中奖人数');
            $table->tinyInteger('status')->default(1)->comment('状态:1=待处理,2=转账中,3=已完成,4=失败,5=部分失败');
            $table->integer('version')->default(0)->comment('乐观锁版本号');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');

            // 索引
            $table->unique('prize_serial_no', 'uk_serial_no');
            $table->index('group_id', 'idx_group_id');
            $table->index('status', 'idx_status');
            $table->index(['group_id', 'wallet_cycle'], 'idx_wallet_cycle');

            $table->comment('中奖记录表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_record');
    }
};
