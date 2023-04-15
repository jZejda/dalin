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
            $table->string('alt_name', 255)->nullable();
            $table->integer('oris_id')->nullable();
            $table->date('date')->nullable();
            $table->string('place')->nullable();
            $table->string('organization')->nullable();
            $table->string('region')->nullable();
            $table->longText('entry_desc')->nullable();
            $table->longText('event_info')->nullable();
            $table->string('event_warning')->nullable();
            $table->unsignedBigInteger('sport_id')->default(1);
            $table->foreign('sport_id')->references('id')->on('sport_lists');
            $table->unsignedBigInteger('discipline_id')->nullable();
            $table->foreign('discipline_id')->references('id')->on('sport_disciplines');
            $table->unsignedBigInteger('level_id')->nullable();
            $table->foreign('level_id')->references('id')->on('sport_levels');
            $table->string('event_type', 32)->nullable();
            $table->boolean('use_oris_for_entries');
            $table->boolean('ranking')->nullable();
            $table->float('ranking_coefficient')->nullable();
            $table->dateTime('entry_date_1')->nullable();
            $table->dateTime('entry_date_2')->nullable();
            $table->dateTime('entry_date_3')->nullable();
            $table->dateTime('last_update')->nullable();
            $table->time('start_time')->nullable();
            $table->string('gps_lat')->nullable();
            $table->string('gps_lon')->nullable();
            $table->json('weather')->nullable();
            $table->integer('parent_id')->nullable();
            $table->boolean('cancelled')->default(false);
            $table->boolean('dont_update_excluded')->default(true);
            $table->timestamps();

            $table->index(['name', 'sport_id', 'entry_date_1', 'date', 'oris_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sport_events');
    }
};
