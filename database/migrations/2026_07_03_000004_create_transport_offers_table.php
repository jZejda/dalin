<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transport_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_event_id')->constrained('sport_events')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->string('departure_place');
            $table->string('direction');
            $table->unsignedSmallInteger('seats_offered');
            $table->unsignedSmallInteger('distance_km')->nullable();
            $table->decimal('contribution', 8, 2)->nullable();
            $table->boolean('active')->default(true)->index();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_offers');
    }
};
