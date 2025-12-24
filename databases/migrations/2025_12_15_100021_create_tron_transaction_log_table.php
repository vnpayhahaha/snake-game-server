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
        Schema::create('tron_transaction_log', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->bigInteger('group_id')->comment('群组ID');
            $table->string('tx_hash', 64)->unique()->comment('交易哈希');
            $table->string('from_address', 34)->comment('发送地址');
            $table->string('to_address', 34)->comment('接收地址');
            $table->decimal('amount', 20, 8)->default(0.00000000)->comment('金额(TRX)');
            $table->tinyInteger('transaction_type')->default(1)->comment('交易类型:1=入账,2=出账');
            $table->bigInteger('block_height')->comment('区块高度');
            $table->bigInteger('block_timestamp')->comment('区块时间戳');
            $table->string('status', 20)->comment('交易状态');
            $table->boolean('is_valid')->default(0)->comment('是否有效交易');
            $table->string('invalid_reason', 255)->nullable()->comment('无效原因');
            $table->boolean('processed')->default(0)->comment('是否已处理');
            $table->dateTime('created_at')->nullable()->comment('创建时间');

            // 索引
            $table->index('group_id', 'idx_group_id');
            $table->index('to_address', 'idx_to_address');
            $table->index('block_height', 'idx_block_height');
            $table->index('processed', 'idx_processed');
            $table->index(['group_id', 'transaction_type'], 'idx_group_tx_type');

            $table->comment('TRON交易监听日志表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tron_transaction_log');
    }
};
