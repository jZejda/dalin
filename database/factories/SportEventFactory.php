<?php

namespace Database\Factories;

use App\Enums\SportEventType;
use App\Models\SportEvent;
use Database\Seeders\SportDisciplinesSeeder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SportEventFactory extends Factory
{
    protected $model = SportEvent::class;

    public function definition(): array
    {
        return [
            'name' => fake()->sentence(3),
            'alt_name' => fake()->optional()->sentence(2),
            'oris_id' => fake()->optional()->numberBetween(1000, 9999),
            'date' => fake()->dateTimeBetween('now', '+1 year'),
            'date_end' => function (array $attributes) {
                return Carbon::parse($attributes['date'])->addDays(fake()->numberBetween(0, 3));
            },
            'place' => fake()->city(),
            'organization' => [fake()->company(), fake()->optional()->company()],
            'region' => [fake()->country()],
            'entry_desc' => fake()->optional()->paragraph(),
            'event_info' => fake()->optional()->paragraph(),
            'event_warning' => fake()->optional()->sentence(),
            'sport_id' => fake()->numberBetween(1, 10),
            'discipline_id' => fake()->optional()->randomElement(
                SportDisciplinesSeeder::getSeederData()
            ),
            'level_id' => fake()->optional()->numberBetween(1, 3),
            'event_type' => fake()->randomElement(SportEventType::cases()),
            'use_oris_for_entries' => fake()->boolean(),
            'ranking' => fake()->optional()->boolean(),
            'ranking_coefficient' => fake()->optional()->randomFloat(2, 0.5, 2.0),
            'entry_date_1' => fake()->dateTimeBetween('now', '+2 months'),
            'entry_date_2' => function (array $attributes) {
                return Carbon::parse($attributes['entry_date_1'])->addDays(fake()->numberBetween(5, 10));
            },
            'entry_date_3' => function (array $attributes) {
                return Carbon::parse($attributes['entry_date_2'])->addDays(fake()->numberBetween(5, 10));
            },
            'increase_entry_fee_2' => fake()->optional()->numberBetween(100, 500),
            'increase_entry_fee_3' => fake()->optional()->numberBetween(500, 1000),
            'last_calculate_cost' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
            'start_time' => fake()->time('H:i:s'),
            'gps_lat' => fake()->optional()->latitude(),
            'gps_lon' => fake()->optional()->longitude(),
            'weather' => fake()->optional()->boolean() ? [
                'temperature' => fake()->numberBetween(10, 30),
                'conditions' => fake()->randomElement(['Sunny', 'Cloudy', 'Rainy']),
            ] : null,
            'parent_id' => null,
            'stages' => fake()->optional()->numberBetween(1, 5),
            'multi_events' => fake()->optional()->numberBetween(1, 3),
            'cancelled' => fake()->boolean(10), // 10% chance of being cancelled
            'cancelled_reason' => function (array $attributes) {
                return $attributes['cancelled'] ? fake()->sentence() : null;
            },
            'dont_update_excluded' => fake()->boolean(20), // 20% chance of being true
            'last_update' => fake()->dateTimeBetween('-1 month', 'now'),
            'created_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return Carbon::parse($attributes['created_at'])->addDays(fake()->numberBetween(0, 30));
            },
        ];
    }

    /**
     * Indicate that the event is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'cancelled' => true,
            'cancelled_reason' => fake()->sentence(),
        ]);
    }

    /**
     * Indicate that the event uses ORIS for entries.
     */
    public function useOrisForEntries(): static
    {
        return $this->state(fn (array $attributes) => [
            'use_oris_for_entries' => true,
            'oris_id' => fake()->numberBetween(1000, 9999),
        ]);
    }

    /**
     * Configure the event as a ranking event.
     */
    public function ranked(): static
    {
        return $this->state(fn (array $attributes) => [
            'ranking' => true,
            'ranking_coefficient' => fake()->randomFloat(2, 0.5, 2.0),
        ]);
    }
}
