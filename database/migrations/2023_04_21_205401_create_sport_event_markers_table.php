<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sport_event_markers', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('external_key')->nullable();
            $table->unsignedBigInteger('sport_event_id');
            $table->foreign('sport_event_id')->references('id')->on('sport_events');
            $table->string('letter', 1)->nullable();
            $table->string('label', 255);
            $table->longText('desc')->nullable();
            $table->float('lat', 10);
            $table->float('lon', 10);
            $table->string('type', 36)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sport_event_markers');
    }
};
