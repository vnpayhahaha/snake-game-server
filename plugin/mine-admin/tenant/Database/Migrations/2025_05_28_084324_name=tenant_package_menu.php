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
        Schema::create('tenant_package_menu', function (Blueprint $table) {
            // 套餐菜单关联表
            $table->unsignedBigInteger('package_id')->comment('套餐ID');
            $table->unsignedBigInteger('menu_id')->comment('菜单ID');
            $table->primary(['package_id', 'menu_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saas_package_menu');
    }
};
