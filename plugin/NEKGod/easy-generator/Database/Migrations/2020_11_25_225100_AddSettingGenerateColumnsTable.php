<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class AddSettingGenerateColumnsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if(Schema::hasTable('setting_generate_columns')) {
            Schema::table('setting_generate_columns', function (Blueprint $table) {
                if (! Schema::hasColumn('setting_generate_columns','query_form_type')) {
                    $table->addColumn('string', 'query_form_type', ['length' => 100, 'comment' => '搜索控件'])->nullable();
                }
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void{}
}
