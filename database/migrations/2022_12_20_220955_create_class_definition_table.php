<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('sport_class_definitions', function (Blueprint $table) {
            $table->id();
            $table->integer('oris_id')->nullable();
            $table->unsignedBigInteger('sport_id');
            $table->foreign('sport_id')->references('id')->on('sport_lists');
            $table->integer('age_from');
            $table->integer('age_to');
            $table->string('gender')->nullable();
            $table->string('name')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sport_class_definitions');
    }
};
