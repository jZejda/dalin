<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SportClass>
 */
class SportClassFactory extends Factory
{
    public function definition(): array
    {
        return [
            'sport_event_id' => SportEvent::factory(),
            'oris_id' => null,
            'class_definition_id' => fn (): int => (int) (SportClassDefinition::query()->inRandomOrder()->value('id')
                ?? SportClassDefinition::query()->create([
                    'sport_id' => 1,
                    'name'     => 'H21',
                    'gender'   => 'M',
                    'age_from' => 21,
                    'age_to'   => 34,
                ])->id),
            'name' => function (array $attributes): ?string {
                return SportClassDefinition::query()->whereKey($attributes['class_definition_id'])->value('name');
            },
            'distance' => (string) $this->faker->numberBetween(2000, 15000),
            'climbing' => (string) $this->faker->numberBetween(50, 600),
            'controls' => (string) $this->faker->numberBetween(8, 30),
            'fee'      => $this->faker->randomElement([100.0, 150.0, 200.0, 250.0]),
            'legs'     => null,
        ];
    }

    public function relay(int $legs = 3): static
    {
        return $this->state(fn (): array => [
            'legs' => $legs,
        ]);
    }
}
