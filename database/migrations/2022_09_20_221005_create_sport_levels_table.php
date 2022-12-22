<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('sport_levels', function (Blueprint $table) {
            $table->id();
            $table->integer('oris_id')->nullable();
            $table->string('short_name', 4);
            $table->string('long_name', 120);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sport_levels');
    }
};
