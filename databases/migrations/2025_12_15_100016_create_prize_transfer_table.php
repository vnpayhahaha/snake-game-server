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
        Schema::create('prize_transfer', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->bigInteger('prize_record_id')->comment('中奖记录ID');
            $table->string('prize_serial_no', 30)->default('')->comment('开奖流水号');
            $table->bigInteger('node_id')->comment('中奖节点ID');
            $table->string('to_address', 34)->comment('收款地址');
            $table->decimal('amount', 20, 8)->default(0.00000000)->comment('转账金额');
            $table->string('tx_hash', 64)->nullable()->comment('转账交易哈希');
            $table->tinyInteger('status')->default(1)->comment('状态:1=待处理,2=处理中,3=成功,4=失败');
            $table->integer('retry_count')->default(0)->comment('重试次数');
            $table->text('error_message')->nullable()->comment('错误信息');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');

            // 索引
            $table->index('prize_record_id', 'idx_prize_record');
            $table->index('prize_serial_no', 'idx_serial_no');
            $table->index('status', 'idx_status');
            $table->index('tx_hash', 'idx_tx_hash');

            $table->comment('奖金转账记录表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_transfer');
    }
};
