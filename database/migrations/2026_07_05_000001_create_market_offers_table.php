<?php

declare(strict_types=1);

use App\Enums\MarketOfferStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('market_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->boolean('is_club_offer')->default(false);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default(MarketOfferStatus::Active->value)->index();
            $table->dateTime('closes_at');
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_offers');
    }
};
