<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('user_entries', function (Blueprint $table) {
            $table->id();
            $table->integer('oris_entry_id')->nullable();
            $table->unsignedBigInteger('sport_event_id');
            $table->foreign('sport_event_id')->references('id')->on('sport_events');
            $table->unsignedBigInteger('class_definition_id');
            $table->foreign('class_definition_id')->references('id')->on('sport_class_definitions');
            $table->unsignedBigInteger('user_race_profile_id');
            $table->foreign('user_race_profile_id')->references('id')->on('user_race_profiles');
            $table->string('class_name', 60)->nullable();
            $table->string('note', 255)->nullable();
            $table->text('club_note')->nullable();
            $table->string('requested_start')->nullable();
            $table->integer('si')->nullable();
            $table->boolean('rent_si')->default(0);
            $table->integer('stage_x')->nullable();
            $table->string('entry_status')->default('created');
            $table->boolean('entry_lock')->default(0);
            $table->dateTime('entry_created')->nullable();
            $table->timestamps();

            $table->index(['sport_event_id', 'user_race_profile_id', 'si']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_entries');
    }
};
