<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('sport_event_exports', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 255);
            $table->string('export_type', 60);
            $table->dateTime('start_time')->nullable();
            $table->integer('sport_event_id')->nullable();
            $table->integer('sport_event_leg_id')->nullable();
            $table->string('file_type', 50);
            $table->string('result_path', 255);
            $table->timestamps();

            $table->index(['title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sport_event_exports');
    }
};
