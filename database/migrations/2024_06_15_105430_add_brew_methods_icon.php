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
        Schema::table('brew_methods', function (Blueprint $table) {
            $table->string('icon')->after('method')->comment('冲泡方法的图标');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brew_methods', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
};
