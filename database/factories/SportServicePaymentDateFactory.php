<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SportServicePaymentDate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SportServicePaymentDate>
 */
class SportServicePaymentDateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'payment_date' => now()->addDays(14)->toDateString(),
            'description' => $this->faker->sentence(),
        ];
    }
}
