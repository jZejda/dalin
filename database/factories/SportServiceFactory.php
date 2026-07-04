<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SportService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SportService>
 */
class SportServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'oris_service_id' => $this->faker->unique()->numberBetween(1000, 99999),
            'service_name_cz' => $this->faker->randomElement(['Ubytování', 'Nocleh v tělocvičně', 'Parkování', 'Tričko']),
            'last_booking_date_time' => now()->addWeek()->format('Y-m-d H:i:s'),
            'unit_price' => $this->faker->randomFloat(2, 50, 500),
            'qty_available' => 100,
            'qty_already_ordered' => 0,
            'qty_remaining' => 100,
        ];
    }
}
