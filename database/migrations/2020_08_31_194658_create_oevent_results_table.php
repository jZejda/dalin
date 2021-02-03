<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOeventResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oevent_results', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('oevent_id');
            $table->foreign('oevent_id')->references('id')->on('oevents');
            $table->unsignedBigInteger('oevent_leg_id');
            $table->foreign('oevent_leg_id')->references('id')->on('oevent_legs');
            $table->string('result_type', 50);
            $table->string('result_path', 255);
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
        Schema::dropIfExists('oevent_results');
    }
}
