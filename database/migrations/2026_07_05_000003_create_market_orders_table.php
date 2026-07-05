<?php

declare(strict_types=1);

use App\Enums\MarketOrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('market_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_product_id')->constrained('market_products')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->unsignedSmallInteger('qty')->default(1);
            $table->decimal('unit_price', 10, 2);
            $table->string('note')->nullable();
            $table->string('status')->default(MarketOrderStatus::Ordered->value)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_orders');
    }
};
