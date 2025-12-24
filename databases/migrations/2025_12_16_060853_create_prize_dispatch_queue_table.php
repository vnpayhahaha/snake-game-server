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
        Schema::create('prize_dispatch_queue', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('主键');
            $table->bigInteger('prize_record_id')->comment('中奖记录ID');
            $table->bigInteger('prize_transfer_id')->comment('转账记录ID');
            $table->bigInteger('group_id')->comment('群组ID');
            $table->string('prize_serial_no', 30)->default('')->comment('开奖流水号');
            $table->tinyInteger('priority')->default(5)->comment('优先级(1-10,数字越小优先级越高)');
            $table->tinyInteger('status')->default(1)->comment('状态:1=待处理,2=处理中,3=已完成,4=失败,5=取消');
            $table->integer('retry_count')->default(0)->comment('重试次数');
            $table->integer('max_retry')->default(3)->comment('最大重试次数');
            $table->text('task_data')->nullable()->comment('任务数据(JSON格式)');
            $table->text('error_message')->nullable()->comment('错误信息');
            $table->dateTime('scheduled_at')->nullable()->comment('计划执行时间');
            $table->dateTime('started_at')->nullable()->comment('开始处理时间');
            $table->dateTime('completed_at')->nullable()->comment('完成时间');
            $table->integer('version')->default(0)->comment('乐观锁版本号');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');

            // 索引
            $table->index('prize_record_id', 'idx_prize_record');
            $table->index('prize_transfer_id', 'idx_prize_transfer');
            $table->index('group_id', 'idx_group_id');
            $table->index('prize_serial_no', 'idx_serial_no');
            $table->index('status', 'idx_status');
            $table->index(['status', 'priority', 'scheduled_at'], 'idx_queue_process');
            $table->index('created_at', 'idx_created_at');

            $table->comment('奖励发放任务队列表');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_dispatch_queue');
    }
};
