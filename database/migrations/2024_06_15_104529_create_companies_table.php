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
        Schema::create('companies', function (Blueprint $table) {
            // 公司表 存放咖啡店所属公司的公共属性
            $table->comment('公司表');
            $table->increments('id');
            $table->string('name');
            $table->integer('roaster');
            $table->text('website');
            $table->text('logo');
            $table->text('description');
            $table->integer('added_by')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
