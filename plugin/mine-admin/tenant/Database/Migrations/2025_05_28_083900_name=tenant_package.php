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
        // 多租户参套表
        Schema::create('tenant_package', function (Blueprint $table) {
            $table->comment('多租户套餐');
            $table->bigIncrements('id')->comment('套餐ID');
            $table->string('package_name', 20)->unique()->comment('套餐名称')->unique();
            $table->tinyInteger('status')->default(1)->comment('状态:1=正常,2=停用');
            $table->authorBy();
            $table->datetimes();
            $table->string('remark', 255)->default('')->comment('备注');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saas_package');
    }
};
