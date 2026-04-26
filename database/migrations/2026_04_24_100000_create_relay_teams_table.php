<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('relay_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sport_event_id');
            $table->foreign('sport_event_id')->references('id')->on('sport_events')->cascadeOnDelete();
            $table->unsignedBigInteger('sport_class_id')->nullable();
            $table->foreign('sport_class_id')->references('id')->on('sport_classes')->nullOnDelete();
            $table->string('name', 80);
            $table->string('relay_type', 8)->default('ST');
            $table->unsignedTinyInteger('slots_count')->default(3);
            $table->timestamps();

            $table->unique(['sport_event_id', 'name']);
            $table->index(['sport_event_id', 'sport_class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('relay_teams');
    }
};
