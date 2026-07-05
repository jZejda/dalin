<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\ServiceOrderStatus;
use App\Models\SportService;
use App\Models\SportServiceOrder;
use App\Models\SportServicePaymentDate;
use App\Models\User;
use App\Models\UserRaceProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SportServiceOrder>
 */
class SportServiceOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sport_service_id' => SportService::factory(),
            'sport_event_id' => fn (array $attributes): int => (int) SportService::query()
                ->whereKey($attributes['sport_service_id'])
                ->value('sport_event_id'),
            'sport_service_payment_date_id' => fn (array $attributes): int => SportServicePaymentDate::factory()
                ->create(['sport_service_id' => $attributes['sport_service_id']])
                ->id,
            'user_id' => User::factory(),
            'user_race_profile_id' => fn (array $attributes): int => UserRaceProfile::factory()
                ->create(['user_id' => $attributes['user_id']])
                ->id,
            'source_user_id' => fn (array $attributes): int => (int) $attributes['user_id'],
            'qty' => 1,
            'unit_price' => $this->faker->randomFloat(2, 50, 500),
            'note' => null,
            'oris_service_entry_id' => null,
            'status' => ServiceOrderStatus::Ordered,
        ];
    }
}
