<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sport_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sport_event_id');
            $table->foreign('sport_event_id')->references('id')->on('sport_events');
            $table->integer('oris_service_id')->nullable();
            $table->string('service_name_cz')->nullable();
            $table->dateTime('last_booking_date_time');
            $table->float('unit_price');
            $table->integer('qty_available')->unsigned()->nullable();
            $table->integer('qty_already_ordered')->unsigned()->nullable();
            $table->integer('qty_remaining')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sport_services');
    }
};
