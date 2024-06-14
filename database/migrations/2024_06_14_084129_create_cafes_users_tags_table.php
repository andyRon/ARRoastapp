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
        Schema::create('cafes_users_tags', function (Blueprint $table) {
            $table->comment('标签、咖啡店、用户关系表');
            $table->integer('cafe_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('tag_id')->unsigned();
            $table->primary(['cafe_id', 'user_id', 'tag_id'], 'cafes_users_tags_primary');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafes_users_tags');
    }
};
