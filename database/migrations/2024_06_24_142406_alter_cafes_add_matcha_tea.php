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
        Schema::table('cafes', function (Blueprint $table) {
            $table->integer('added_by')->after('zip')->unsigned()->nullable();
            $table->tinyInteger('tea')->after('added_by');
            $table->tinyInteger('matcha')->after('tea');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cafes', function (Blueprint $table) {
            $table->dropColumn('added_by');
            $table->dropColumn('matcha');
            $table->dropColumn('tea');
        });
    }
};
