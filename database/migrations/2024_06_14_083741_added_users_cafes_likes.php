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
        // 咖啡店和用户之间的关系
        Schema::create('users_cafes_likes', function (Blueprint $table) {
            $table->comment('用户喜欢表');
            $table->integer('user_id')->unsigned();
            $table->integer('cafe_id')->unsigned();
            $table->primary(['user_id', 'cafe_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_cafes_likes');
    }
};
