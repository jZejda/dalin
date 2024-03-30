<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transport_offers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('sport_event_id')->nullable();
            $table->foreign('sport_event_id')->references('id')->on('sport_events');

            $table->string('direction', 32);
            $table->tinyInteger('free_seats');
            $table->tinyInteger('distance')->nullable();
            $table->float('transport_contribution')->nullable();
            $table->string('description')->nullable();
            //$table->uuid('uid')->default(DB::raw('(UUID())'));

            $table->timestamps();
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
