<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('user_credits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('related_user_id')->nullable();
            $table->foreign('related_user_id')->references('id')->on('users');
            $table->unsignedBigInteger('user_race_profile_id')->nullable();
            $table->foreign('user_race_profile_id')->references('id')->on('user_race_profiles');
            $table->unsignedBigInteger('sport_event_id')->nullable();
            $table->foreign('sport_event_id')->references('id')->on('sport_events');
            $table->unsignedBigInteger('sport_service_id')->nullable();
            $table->foreign('sport_service_id')->references('id')->on('sport_services');
            $table->unsignedBigInteger('bank_transaction_id')->nullable();
            $table->foreign('bank_transaction_id')->references('id')->on('bank_transactions');
            $table->integer('oris_balance_id')->nullable();
            $table->string('status')->default('done');
            $table->float('amount');
            $table->float('balance')->nullable();
            $table->string('currency', 5)->default('CZK');
            $table->string('source', 60)->default('user');
            $table->unsignedBigInteger('source_user_id')->nullable();
            $table->foreign('source_user_id')->references('id')->on('users');
            $table->string('credit_type', 60);
            $table->timestamps();

            $table->index(['user_id', 'source_user_id', 'sport_event_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_credits');
    }
};
