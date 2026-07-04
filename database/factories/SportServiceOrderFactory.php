<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ServiceOrderStatus;
use App\Models\SportServiceOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SportServiceOrder>
 */
class SportServiceOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'qty' => 1,
            'unit_price' => $this->faker->randomFloat(2, 50, 500),
            'note' => null,
            'oris_service_entry_id' => null,
            'status' => ServiceOrderStatus::Ordered,
        ];
    }
}
