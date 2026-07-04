<?php

declare(strict_types=1);

use App\Enums\ServiceOrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sport_service_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_event_id')->constrained('sport_events')->cascadeOnDelete();
            $table->foreignId('sport_service_id')->constrained('sport_services')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('user_race_profile_id')->constrained('user_race_profiles');
            $table->foreignId('sport_service_payment_date_id')->constrained('sport_service_payment_dates');
            $table->unsignedSmallInteger('qty')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->string('note')->nullable();
            $table->unsignedBigInteger('oris_service_entry_id')->nullable();
            $table->string('status')->default(ServiceOrderStatus::Ordered->value)->index();
            $table->foreignId('source_user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sport_service_orders');
    }
};
