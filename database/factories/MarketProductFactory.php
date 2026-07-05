<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MarketPaymentMethod;
use App\Models\MarketOffer;
use App\Models\MarketProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketProduct>
 */
class MarketProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'market_offer_id' => MarketOffer::factory(),
            'name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'url' => null,
            'unit_price' => $this->faker->randomFloat(2, 100, 2000),
            'payment_method' => MarketPaymentMethod::CreditCharge,
            'qty_available' => null,
        ];
    }

    public function limited(int $qty): static
    {
        return $this->state(fn (): array => [
            'qty_available' => $qty,
        ]);
    }

    public function free(): static
    {
        return $this->state(fn (): array => [
            'unit_price' => 0,
        ]);
    }

    public function directPayment(): static
    {
        return $this->state(fn (): array => [
            'payment_method' => MarketPaymentMethod::DirectPayment,
        ]);
    }
}
