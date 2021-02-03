<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOeventLegsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oevent_legs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('oevent_id')->unsigned()->nullable();
            $table->unsignedBigInteger('oris_id');
            $table->foreign('oris_id')->references('id')->on('oevents');
            $table->string('title', 255);
            $table->unsignedBigInteger('discipline_id');
            $table->foreign('discipline_id')->references('id')->on('disciplines');
            $table->dateTime('leg_datetime')->nullable();
            $table->decimal('lat', 10, 8);
            $table->decimal('lon', 11, 8);
            $table->json('forecast')->nullable();
            $table->longText('description')->nullable();;
            $table->tinyInteger('content_format')->default(2);
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
        Schema::dropIfExists('oevent_legs');
    }
}
