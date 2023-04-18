<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('sport_classes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sport_event_id');
            $table->foreign('sport_event_id')->references('id')->on('sport_events');
            $table->integer('oris_id')->nullable();
            $table->unsignedBigInteger('class_definition_id');
            $table->foreign('class_definition_id')->references('id')->on('sport_class_definitions');
            $table->string('name', 16)->nullable();
            $table->string('distance')->nullable();
            $table->string('climbing')->nullable();
            $table->string('controls')->nullable();
            $table->double('fee')->nullable();

            $table->timestamps();

            $table->index(['class_definition_id', 'sport_event_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sport_classes');
    }
};
