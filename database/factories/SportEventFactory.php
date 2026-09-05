<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\SportEventType;
use App\Models\SportDiscipline;
use App\Models\SportEvent;
use App\Models\SportList;
use Faker\Generator;
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
            'sport_id' => fn (): int => (int) (SportList::query()->inRandomOrder()->value('id')
                ?? SportList::query()->create(['short_name' => 'OB'])->id),
            'discipline_id' => fn (): int => (int) (SportDiscipline::query()->inRandomOrder()->value('id')
                ?? SportDiscipline::query()->create(['short_name' => 'LD', 'long_name' => 'Dlouhá trať'])->id),
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
            // Central Europe, so factory-made events land on the map near Czechia
            'gps_lat' => (string) fake()->randomFloat(6, 48.55, 51.05),
            'gps_lon' => (string) fake()->randomFloat(6, 12.09, 18.86),
            'weather' => function (array $attributes) {
                return fake()->boolean(70)
                    ? self::fakeWeather(Carbon::parse($attributes['date']), fake())
                    : null;
            },
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

    /**
     * Build a fake weather payload shaped exactly like OpenMapService's forecast
     * response (App\Http\Components\OpenMap), for demo data and tests. The
     * condition is picked from a season-appropriate weighted pool so the mix
     * of ids/icons looks plausible for the given event date.
     *
     * @return array<string, mixed>
     */
    public static function fakeWeather(Carbon $date, Generator $faker): array
    {
        $pool = self::seasonalWeatherConditions((int) $date->format('n'));

        $weighted = [];
        foreach ($pool as $condition) {
            $weighted = array_merge($weighted, array_fill(0, $condition['weight'], $condition));
        }

        $condition = $faker->randomElement($weighted);
        [$tempMin, $tempMax] = $condition['tempRange'];
        $temp = $faker->randomFloat(2, $tempMin, $tempMax);

        $iconGroup = self::weatherIconGroup((int) $condition['id']);
        $isNight = $faker->boolean(8);
        $icon = $iconGroup.($isNight ? 'n' : 'd');

        $cloudsAll = match (true) {
            $condition['id'] === 800 => $faker->numberBetween(0, 10),
            $condition['id'] === 801 => $faker->numberBetween(11, 25),
            $condition['id'] === 802 => $faker->numberBetween(26, 50),
            $condition['id'] === 803 => $faker->numberBetween(51, 84),
            default => $faker->numberBetween(70, 100),
        };

        $isStormy = in_array($condition['id'], [200, 201, 202, 210, 211, 212, 221, 230, 231, 232, 771], true);
        $windSpeed = $isStormy ? $faker->randomFloat(2, 5, 15) : $faker->randomFloat(2, 0.5, 6);

        $isMisty = in_array($condition['id'], [701, 711, 721, 731, 741, 751, 761], true);
        $visibility = $isMisty ? $faker->numberBetween(800, 4000) : 10000;

        $dtTxt = $date->copy()->setTime(9, 0);

        $payload = [
            'dt' => $dtTxt->timestamp,
            'pop' => in_array($iconGroup, ['09', '10', '11', '13'], true) ? $faker->randomFloat(2, 0.2, 0.9) : 0,
            'sys' => ['pod' => $isNight ? 'n' : 'd'],
            'main' => [
                'temp' => $temp,
                'temp_kf' => 0,
                'humidity' => $faker->numberBetween(35, 95),
                'pressure' => $faker->numberBetween(995, 1030),
                'temp_max' => $temp,
                'temp_min' => $temp,
                'sea_level' => $faker->numberBetween(995, 1030),
                'feels_like' => $temp - $faker->randomFloat(1, 0, 2),
                'grnd_level' => $faker->numberBetween(950, 1000),
            ],
            'wind' => [
                'deg' => $faker->numberBetween(0, 359),
                'gust' => $windSpeed + $faker->randomFloat(2, 0.5, 4),
                'speed' => $windSpeed,
            ],
            'clouds' => ['all' => $cloudsAll],
            'dt_txt' => $dtTxt->format('Y-m-d H:i:s'),
            'weather' => [[
                'id' => $condition['id'],
                'icon' => $icon,
                'main' => $condition['main'],
                'description' => $condition['description'],
            ]],
            'visibility' => $visibility,
        ];

        if (in_array($iconGroup, ['09', '10'], true)) {
            $payload['rain'] = ['3h' => $faker->randomFloat(2, 0.1, 3)];
        }

        if ($iconGroup === '13') {
            $payload['snow'] = ['3h' => $faker->randomFloat(2, 0.1, 3)];
        }

        return $payload;
    }

    /**
     * OpenWeatherMap icon group (without the d/n suffix) for a condition id.
     * Mirrors App\Enums\WeatherIconType's grouping logic.
     */
    private static function weatherIconGroup(int $id): string
    {
        return match (true) {
            $id === 800 => '01',
            $id === 801 => '02',
            $id === 802 => '03',
            in_array($id, [803, 804], true) => '04',
            in_array($id, [300, 301, 302, 310, 311, 312, 313, 314, 321], true) => '09',
            in_array($id, [500, 501, 502, 503, 504, 520, 521, 522, 531], true) => '10',
            in_array($id, [200, 201, 202, 210, 211, 212, 221, 230, 231, 232], true) => '11',
            in_array($id, [511, 600, 601, 602, 611, 612, 613, 615, 616, 620, 621, 622], true) => '13',
            default => '50',
        };
    }

    /**
     * Weighted pool of plausible weather conditions for the Czech Republic,
     * split by season, each entry shaped as
     * {id, main, description (cz), tempRange: [min, max], weight}.
     *
     * @return list<array{id: int, main: string, description: string, tempRange: array{0: int, 1: int}, weight: int}>
     */
    private static function seasonalWeatherConditions(int $month): array
    {
        return match (true) {
            in_array($month, [12, 1, 2], true) => [
                ['id' => 804, 'main' => 'Clouds', 'description' => 'zataženo', 'tempRange' => [-3, 4], 'weight' => 4],
                ['id' => 803, 'main' => 'Clouds', 'description' => 'oblačno', 'tempRange' => [-3, 5], 'weight' => 3],
                ['id' => 800, 'main' => 'Clear', 'description' => 'jasno', 'tempRange' => [-9, 3], 'weight' => 2],
                ['id' => 600, 'main' => 'Snow', 'description' => 'slabé sněžení', 'tempRange' => [-6, 2], 'weight' => 3],
                ['id' => 601, 'main' => 'Snow', 'description' => 'sněžení', 'tempRange' => [-8, 0], 'weight' => 2],
                ['id' => 611, 'main' => 'Sleet', 'description' => 'plískanice', 'tempRange' => [-1, 3], 'weight' => 1],
                ['id' => 741, 'main' => 'Fog', 'description' => 'mlha', 'tempRange' => [-4, 2], 'weight' => 2],
            ],
            in_array($month, [3, 4, 5], true) => [
                ['id' => 800, 'main' => 'Clear', 'description' => 'jasno', 'tempRange' => [5, 19], 'weight' => 3],
                ['id' => 801, 'main' => 'Clouds', 'description' => 'skoro jasno', 'tempRange' => [4, 18], 'weight' => 3],
                ['id' => 802, 'main' => 'Clouds', 'description' => 'polojasno', 'tempRange' => [4, 17], 'weight' => 3],
                ['id' => 803, 'main' => 'Clouds', 'description' => 'oblačno', 'tempRange' => [3, 15], 'weight' => 3],
                ['id' => 804, 'main' => 'Clouds', 'description' => 'zataženo', 'tempRange' => [3, 14], 'weight' => 2],
                ['id' => 500, 'main' => 'Rain', 'description' => 'slabý déšť', 'tempRange' => [4, 14], 'weight' => 3],
                ['id' => 501, 'main' => 'Rain', 'description' => 'déšť', 'tempRange' => [3, 12], 'weight' => 2],
                ['id' => 300, 'main' => 'Drizzle', 'description' => 'slabé mrholení', 'tempRange' => [4, 13], 'weight' => 1],
                ['id' => 200, 'main' => 'Thunderstorm', 'description' => 'bouřka s mírným deštěm', 'tempRange' => [9, 19], 'weight' => 1],
            ],
            in_array($month, [6, 7, 8], true) => [
                ['id' => 800, 'main' => 'Clear', 'description' => 'jasno', 'tempRange' => [16, 31], 'weight' => 4],
                ['id' => 801, 'main' => 'Clouds', 'description' => 'skoro jasno', 'tempRange' => [15, 29], 'weight' => 3],
                ['id' => 802, 'main' => 'Clouds', 'description' => 'polojasno', 'tempRange' => [14, 27], 'weight' => 3],
                ['id' => 803, 'main' => 'Clouds', 'description' => 'oblačno', 'tempRange' => [13, 25], 'weight' => 2],
                ['id' => 804, 'main' => 'Clouds', 'description' => 'zataženo', 'tempRange' => [13, 22], 'weight' => 1],
                ['id' => 211, 'main' => 'Thunderstorm', 'description' => 'bouřka', 'tempRange' => [17, 27], 'weight' => 2],
                ['id' => 200, 'main' => 'Thunderstorm', 'description' => 'bouřka s mírným deštěm', 'tempRange' => [16, 25], 'weight' => 2],
                ['id' => 500, 'main' => 'Rain', 'description' => 'slabý déšť', 'tempRange' => [13, 22], 'weight' => 2],
            ],
            default => [ // podzim: 9, 10, 11
                ['id' => 804, 'main' => 'Clouds', 'description' => 'zataženo', 'tempRange' => [4, 15], 'weight' => 4],
                ['id' => 803, 'main' => 'Clouds', 'description' => 'oblačno', 'tempRange' => [4, 16], 'weight' => 3],
                ['id' => 802, 'main' => 'Clouds', 'description' => 'polojasno', 'tempRange' => [3, 16], 'weight' => 2],
                ['id' => 800, 'main' => 'Clear', 'description' => 'jasno', 'tempRange' => [2, 17], 'weight' => 2],
                ['id' => 500, 'main' => 'Rain', 'description' => 'slabý déšť', 'tempRange' => [3, 14], 'weight' => 3],
                ['id' => 501, 'main' => 'Rain', 'description' => 'déšť', 'tempRange' => [2, 11], 'weight' => 2],
                ['id' => 741, 'main' => 'Fog', 'description' => 'mlha', 'tempRange' => [0, 10], 'weight' => 3],
                ['id' => 600, 'main' => 'Snow', 'description' => 'slabé sněžení', 'tempRange' => [-2, 4], 'weight' => 1],
            ],
        };
    }
}
