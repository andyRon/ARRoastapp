<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 从 cafes 表中抽走了咖啡店的公共属性，分配到公司表和城市表中
        Schema::table('cafes', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('roaster');
            $table->dropColumn('website');
            $table->dropColumn('description');
            $table->dropColumn('added_by');
            $table->dropColumn('parent');
            $table->integer('company_id')->unsigned()->default(0);
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            $table->string('name')->after('id');
            $table->integer('roaster')->after('longitude');
            $table->text('website')->after('roaster');
            $table->text('description')->after('website');
            $table->integer('added_by')->after('description')->unsigned()->nullable();
            $table->integer('parent')->unsigned()->nullable()->after('id');
            $table->dropColumn('company_id');
            $table->dropColumn('deleted_at');
        });
    }
};
