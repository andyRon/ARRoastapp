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
        Schema::create('cafes_brew_methods', function (Blueprint $table) {
            $table->comment('咖啡冲泡方法关系表');
            $table->integer('cafe_id')->unsigned();
            $table->integer('brew_method_id')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafes_brew_methods');
    }
};
