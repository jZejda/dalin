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
        Schema::create('sport_service_payment_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_service_id')->constrained('sport_services')->cascadeOnDelete();
            $table->date('payment_date');
            $table->string('description');
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sport_service_payment_dates');
    }
};
