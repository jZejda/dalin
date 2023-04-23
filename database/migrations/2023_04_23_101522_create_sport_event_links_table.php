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
        Schema::create('sport_event_links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sport_event_id');
            $table->foreign('sport_event_id')->references('id')->on('sport_events');
            $table->unsignedMediumInteger('external_key')->nullable();
            $table->boolean('internal')->default(true);
            $table->string('source_path')->nullable();
            $table->string('source_url')->nullable();
            $table->string('source_type');
            $table->string('name_cz')->nullable();
            $table->string('name_en')->nullable();
            $table->string('description_cz')->nullable();
            $table->string('description_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sport_event_links');
    }
};
