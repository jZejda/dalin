<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sport_events', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('oris_id')->nullable();
            $table->date('date');
            $table->string('place')->nullable();
            $table->string('region')->nullable();
            $table->integer('sport_id')->default(1);
            $table->unsignedBigInteger('discipline_id');
            $table->foreign('discipline_id')->references('id')->on('sport_disciplines');
            $table->boolean('use_oris_for_entries');
            $table->boolean('ranking')->nullable();
            $table->float('ranking_coefficient')->nullable();
            $table->dateTime('entry_date_1');
            $table->dateTime('entry_date_2')->nullable();
            $table->dateTime('entry_date_3')->nullable();
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
        Schema::dropIfExists('sport_events');
    }
};
