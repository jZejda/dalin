<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained();
            $table->unsignedBigInteger('content_category_id');
            $table->foreign('content_category_id')->references('id')->on('content_categories');
            $table->string('title', 255);
            $table->longText('content');
            $table->string('status', 10)->default('close');
            $table->tinyInteger('weight')->default(50);
            $table->tinyInteger('page_menu')->default(0)->unsigned();
            $table->tinyInteger('content_format')->default(1);
            $table->string('slug')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
