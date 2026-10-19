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
        Schema::create('sport_event_oris_statuses', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('sport_event_id')->unique()->constrained('sport_events')->cascadeOnDelete();
            // ORIS getEvent
            $table->string('bank_account', 64)->nullable();
            $table->unsignedInteger('starts_count')->default(0);
            $table->unsignedInteger('results_count')->default(0);
            // ORIS getEventBalance, club row — all null when the club has no entries in the event
            $table->decimal('fee_entry', 10, 2)->nullable();
            $table->decimal('fee_si', 10, 2)->nullable();
            $table->decimal('fee_service', 10, 2)->nullable();
            $table->decimal('fee_total', 10, 2)->nullable();
            $table->decimal('paid', 10, 2)->nullable();
            $table->decimal('to_be_paid', 10, 2)->nullable();
            $table->string('currency', 3)->default('CZK');
            $table->string('payment_vs', 32)->nullable();
            $table->timestamp('fetched_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sport_event_oris_statuses');
    }
};
