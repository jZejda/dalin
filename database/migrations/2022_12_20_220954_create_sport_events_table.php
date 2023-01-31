<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('sport_events', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->integer('oris_id')->nullable();
            $table->date('date');
            $table->string('place')->nullable();
            $table->string('region')->nullable();
            $table->unsignedBigInteger('sport_id')->default(1);
            $table->foreign('sport_id')->references('id')->on('sport_lists');
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

    public function down(): void
    {
        Schema::dropIfExists('sport_events');
    }
};
