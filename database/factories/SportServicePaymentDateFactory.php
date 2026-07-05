<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SportService;
use App\Models\SportServicePaymentDate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SportServicePaymentDate>
 */
class SportServicePaymentDateFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sport_service_id' => SportService::factory(),
            'payment_date' => now()->addDays(14)->toDateString(),
            'description' => $this->faker->sentence(),
            'created_by_user_id' => User::factory(),
        ];
    }
}
