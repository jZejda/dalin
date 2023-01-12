<?php

declare(strict_types=1);

use App\Models\UserCredit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_credits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('user_race_profile_id')->nullable();
            $table->foreign('user_race_profile_id')->references('id')->on('user_race_profiles');
            $table->unsignedBigInteger('sport_event_id')->nullable();
            $table->foreign('sport_event_id')->references('id')->on('sport_events');
            $table->unsignedBigInteger('sport_service_id')->nullable();
            $table->foreign('sport_service_id')->references('id')->on('sport_services');
            $table->float('amount');
            $table->enum('currency', [UserCredit::CURRENCY_CZK, UserCredit::CURRENCY_EUR]);
            $table->enum('source', [UserCredit::SOURCE_CRON, UserCredit::SOURCE_USER]);
            $table->unsignedBigInteger('source_user_id')->nullable();
            $table->foreign('source_user_id')->references('id')->on('users');
            $table->enum('credit_type', [
                UserCredit::CREDIT_TYPE_CACHE_IN,
                UserCredit::CREDIT_TYPE_CACHE_OUT,
                UserCredit::CREDIT_TYPE_DONATION,
            ]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_credits');
    }
};
