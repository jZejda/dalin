<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOeventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oevents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('oris_id')->unsigned()->nullable();
            $table->unsignedBigInteger('sport_id');
            $table->foreign('sport_id')->references('id')->on('sports');
            $table->string('title', 255);
            $table->string('place', 255);
            $table->string('url', 255)->nullable();
            $table->dateTime('first_date');
            $table->dateTime('second_date')->nullable();
            $table->dateTime('third_date')->nullable();
            $table->date('from_date');
            $table->date('to_date')->nullable();
            $table->json('clubs')->nullable();
            $table->json('regions')->nullable();
            $table->integer('event_category')->unsigned()->nullable();
            //$table->integer('event_type')->unsigned()->nullable();
            $table->longText('description')->nullable();;
            $table->tinyInteger('content_format')->default(2);
            $table->tinyInteger('is_canceled')->default(0);
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
        Schema::dropIfExists('oevents');
    }
}
