<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\MarketOrderStatus;
use App\Models\MarketOrder;
use App\Models\MarketProduct;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MarketOrder>
 */
class MarketOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'market_product_id' => MarketProduct::factory(),
            'user_id' => User::factory(),
            'qty' => 1,
            'unit_price' => $this->faker->randomFloat(2, 100, 2000),
            'note' => null,
            'status' => MarketOrderStatus::Ordered,
        ];
    }

    public function cancelled(): static
    {
        return $this->state(fn (): array => [
            'status' => MarketOrderStatus::Cancelled,
        ]);
    }

    public function billed(): static
    {
        return $this->state(fn (): array => [
            'status' => MarketOrderStatus::Billed,
        ]);
    }
}
