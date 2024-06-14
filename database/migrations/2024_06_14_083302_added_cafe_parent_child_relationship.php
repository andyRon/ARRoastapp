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
        // 通过字段parent，将分店与总店映射成了父子关联关系
        Schema::table('cafes', function( Blueprint $table ){
            $table->integer('parent')->unsigned()->nullable()->after('id');
            $table->string('location_name')->after('name');
            $table->integer('roaster')->after('longitude');
            $table->text('website')->after('roaster');
            $table->text('description')->after('website');
            $table->integer('added_by')->after('description')->unsigned()->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cafes', function( Blueprint $table ){
            $table->dropColumn('parent');
            $table->dropColumn('location_name');
            $table->dropColumn('roaster');
            $table->dropColumn('website');
            $table->dropColumn('description');
            $table->dropColumn('added_by');
        });
    }
};
