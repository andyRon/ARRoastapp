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
        Schema::create('cafes_photos', function (Blueprint $table) {
            $table->comment('咖啡店图片');
            $table->increments('id');
            $table->integer('cafe_id')->unsigned();
            $table->integer('uploaded_by')->unsigned();
            $table->text('file_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cafes_photos');
    }
};
