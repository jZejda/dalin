<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\SportEventType;
use App\Models\SportEvent;
use Database\Factories\SportEventFactory;
use Faker\Generator;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoSportEventSeeder extends Seeder
{
    /** Podíl závodů pořádaných v zahraničí (v procentech). */
    private const FOREIGN_EVENT_CHANCE = 12;

    public function run(): void
    {
        $faker = \Faker\Factory::create('cs_CZ');

        $clubAbbrs = array_column(DemoClubsSeeder::getData(), 'abbr');

        $places        = self::getPlaces();
        $czechPlaces   = array_keys(array_filter($places, fn (array $p): bool => ! ($p['foreign'] ?? false)));
        $foreignPlaces = array_keys(array_filter($places, fn (array $p): bool => $p['foreign'] ?? false));

        $eventNames = [
            'Oblastní přebor', 'Krajský přebor', 'Noční závod', 'Sprintový závod', 'Štafetový závod',
            'Mistrovský závod', 'Pohárový závod', 'Žebříčkový závod', 'Tréninkový závod',
            'Lesní závod', 'Městský sprint', 'Víkendový závod', 'Klubový závod', 'Memoriál',
            'Závod škol', 'Veteránský závod', 'Dálkový závod', 'Středoškolský přebor',
        ];

        $disciplines = [1, 2, 3, 4, 5]; // LD, MD, SP, UD, RE
        $levels = [2, 3, 4, 5, 6, 8];   // ŽA, ŽB, OŽ, E, OST, ČP
        $types = [SportEventType::Race->value, SportEventType::Training->value];

        // 40 past events
        for ($i = 0; $i < 40; $i++) {
            $daysAgo = $faker->numberBetween(7, 365);
            $date    = Carbon::now()->subDays($daysAgo)->startOfDay();
            $place   = $faker->boolean(self::FOREIGN_EVENT_CHANCE)
                ? $faker->randomElement($foreignPlaces)
                : $faker->randomElement($czechPlaces);
            $eventName = $faker->randomElement($eventNames);
            $name      = $eventName . ' ' . $place . ' ' . $date->format('Y');
            [$lat, $lon] = self::coordsFor($place, $faker);
            [$entryDate1, $entryDate2, $entryDate3] = self::entryDates($date, $faker);

            SportEvent::create([
                'name'                => $name,
                'alt_name'            => self::altName($eventNames, $eventName, $place, $date, $faker),
                'oris_id'             => null,
                'date'                => $date->toDateString(),
                'place'               => $place,
                'gps_lat'             => $lat,
                'gps_lon'             => $lon,
                'sport_id'            => 1,
                'discipline_id'       => $faker->randomElement($disciplines),
                'level_id'            => $faker->randomElement($levels),
                'event_type'          => $faker->randomElement($types),
                'use_oris_for_entries' => false,
                'ranking'             => $faker->boolean(60),
                'ranking_coefficient' => $faker->randomFloat(1, 0.5, 1.5),
                'entry_date_1'        => $entryDate1,
                'entry_date_2'        => $entryDate2,
                'entry_date_3'        => $entryDate3,
                'cancelled'           => $faker->boolean(5),
                'organization'        => $faker->randomElements($clubAbbrs, $faker->numberBetween(1, 2)),
                // Every past event has, at some point, spent time inside the real cron's
                // 5-day forecast window, so (almost) all of them ended up with a stored forecast.
                'weather'             => $faker->boolean(90) ? SportEventFactory::fakeWeather($date, $faker) : null,
            ]);
        }

        // 15 future events — the first 2 land within the next 5 days, so they pick up a
        // forecast just like UpdateEventWeather would once it runs; the rest are further
        // out and stay without one until they enter that window.
        for ($i = 0; $i < 15; $i++) {
            $daysAhead = $i < 2 ? $faker->numberBetween(1, 5) : $faker->numberBetween(7, 180);
            $date      = Carbon::now()->addDays($daysAhead)->startOfDay();
            $place     = $faker->boolean(self::FOREIGN_EVENT_CHANCE)
                ? $faker->randomElement($foreignPlaces)
                : $faker->randomElement($czechPlaces);
            $eventName = $faker->randomElement($eventNames);
            $name      = $eventName . ' ' . $place . ' ' . $date->format('Y');
            [$lat, $lon] = self::coordsFor($place, $faker);
            [$entryDate1, $entryDate2, $entryDate3] = self::entryDates($date, $faker);

            SportEvent::create([
                'name'                => $name,
                'alt_name'            => self::altName($eventNames, $eventName, $place, $date, $faker),
                'oris_id'             => null,
                'date'                => $date->toDateString(),
                'place'               => $place,
                'gps_lat'             => $lat,
                'gps_lon'             => $lon,
                'sport_id'            => 1,
                'discipline_id'       => $faker->randomElement($disciplines),
                'level_id'            => $faker->randomElement($levels),
                'event_type'          => SportEventType::Race->value,
                'use_oris_for_entries' => false,
                'ranking'             => $faker->boolean(70),
                'ranking_coefficient' => $faker->randomFloat(1, 0.5, 1.5),
                'entry_date_1'        => $entryDate1,
                'entry_date_2'        => $entryDate2,
                'entry_date_3'        => $entryDate3,
                'cancelled'           => false,
                'organization'        => $faker->randomElements($clubAbbrs, $faker->numberBetween(1, 2)),
                'weather'             => $daysAhead <= 5 ? SportEventFactory::fakeWeather($date, $faker) : null,
            ]);
        }
    }

    /**
     * Místa závodů se skutečnými souřadnicemi. `foreign` označuje místa mimo ČR —
     * těžiště mapy zůstává v Česku, pár závodů se jede za hranicemi.
     *
     * @return array<string, array{lat: float, lon: float, foreign?: bool}>
     */
    public static function getPlaces(): array
    {
        return [
            'Brno'             => ['lat' => 49.1951, 'lon' => 16.6068],
            'Praha'            => ['lat' => 50.0755, 'lon' => 14.4378],
            'Plzeň'            => ['lat' => 49.7384, 'lon' => 13.3736],
            'Liberec'          => ['lat' => 50.7663, 'lon' => 15.0543],
            'Olomouc'          => ['lat' => 49.5938, 'lon' => 17.2509],
            'Ostrava'          => ['lat' => 49.8209, 'lon' => 18.2625],
            'Hradec Králové'   => ['lat' => 50.2092, 'lon' => 15.8328],
            'České Budějovice' => ['lat' => 48.9745, 'lon' => 14.4744],
            'Pardubice'        => ['lat' => 50.0343, 'lon' => 15.7812],
            'Zlín'             => ['lat' => 49.2265, 'lon' => 17.6706],
            'Jihlava'          => ['lat' => 49.3961, 'lon' => 15.5912],
            'Kladno'           => ['lat' => 50.1471, 'lon' => 14.1028],
            'Tábor'            => ['lat' => 49.4144, 'lon' => 14.6578],
            'Chomutov'         => ['lat' => 50.4600, 'lon' => 13.4178],
            'Znojmo'           => ['lat' => 48.8555, 'lon' => 16.0488],
            'Příbram'          => ['lat' => 49.6899, 'lon' => 14.0104],
            'Písek'            => ['lat' => 49.3088, 'lon' => 14.1475],
            'Prachatice'       => ['lat' => 49.0130, 'lon' => 14.0000],
            'Strakonice'       => ['lat' => 49.2611, 'lon' => 13.9025],
            'Třeboň'           => ['lat' => 49.0034, 'lon' => 14.7700],
            'Český Krumlov'    => ['lat' => 48.8109, 'lon' => 14.3155],
            'Šumava'           => ['lat' => 49.0500, 'lon' => 13.5000],
            'Beskydy'          => ['lat' => 49.5000, 'lon' => 18.4000],
            'Jeseníky'         => ['lat' => 50.0800, 'lon' => 17.2300],
            'Krkonoše'         => ['lat' => 50.7300, 'lon' => 15.6500],
            'Vysočina'         => ['lat' => 49.4500, 'lon' => 15.6000],
            'Podyjí'           => ['lat' => 48.8500, 'lon' => 15.9000],
            'Slavkov u Brna'   => ['lat' => 49.1533, 'lon' => 16.8760],
            'Mikulov'          => ['lat' => 48.8058, 'lon' => 16.6380],
            'Lednice'          => ['lat' => 48.8010, 'lon' => 16.8050],
            'Valtice'          => ['lat' => 48.7410, 'lon' => 16.7550],
            'Pálava'           => ['lat' => 48.8700, 'lon' => 16.6400],
            'Blansko'          => ['lat' => 49.3644, 'lon' => 16.6440],
            'Vyškov'           => ['lat' => 49.2775, 'lon' => 16.9989],
            'Hodonín'          => ['lat' => 48.8489, 'lon' => 17.1327],

            'Bratislava'       => ['lat' => 48.1486, 'lon' => 17.1077, 'foreign' => true],
            'Žilina'           => ['lat' => 49.2231, 'lon' => 18.7394, 'foreign' => true],
            'Banská Bystrica'  => ['lat' => 48.7363, 'lon' => 19.1462, 'foreign' => true],
            'Wien'             => ['lat' => 48.2082, 'lon' => 16.3738, 'foreign' => true],
            'Linz'             => ['lat' => 48.3069, 'lon' => 14.2858, 'foreign' => true],
            'Kraków'           => ['lat' => 50.0647, 'lon' => 19.9450, 'foreign' => true],
            'Wrocław'          => ['lat' => 51.1079, 'lon' => 17.0385, 'foreign' => true],
            'Dresden'          => ['lat' => 51.0504, 'lon' => 13.7373, 'foreign' => true],
        ];
    }

    /**
     * Souřadnice místa s malým rozptylem — závod se běhá v lese u města,
     * ne na náměstí. Sloupce gps_* jsou v DB řetězce.
     *
     * @return array{0: string, 1: string}
     */
    public static function coordsFor(string $place, Generator $faker): array
    {
        $venue = self::getPlaces()[$place] ?? ['lat' => 49.7500, 'lon' => 15.3500];

        return [
            number_format($venue['lat'] + $faker->randomFloat(6, -0.045, 0.045), 6, '.', ''),
            number_format($venue['lon'] + $faker->randomFloat(6, -0.065, 0.065), 6, '.', ''),
        ];
    }

    /**
     * Počet přihlašovacích termínů: v praxi je většinou jeden, občas dva,
     * tři jen u velkých závodů — proto vážený náhodný výběr místo pevných 3.
     * Termíny jsou seřazené sestupně podle blízkosti k závodu (1 nejdřív).
     *
     * @return array{0: Carbon, 1: Carbon|null, 2: Carbon|null}
     */
    private static function entryDates(Carbon $date, Generator $faker): array
    {
        $termCount = $faker->randomElement([1, 1, 1, 1, 1, 2, 2, 2, 3]);

        // Each further term sits closer to the race than the previous one, with
        // at least a couple of days between tiers — mirrors how fee-increase
        // deadlines are staggered in practice.
        $offset1 = $faker->numberBetween(10, 21);
        $offset2 = $termCount >= 2 ? $faker->numberBetween(4, max(4, $offset1 - 3)) : null;
        $offset3 = $termCount >= 3 ? $faker->numberBetween(1, max(1, $offset2 - 2)) : null;

        return [
            $date->copy()->subDays($offset1)->setTime(23, 59),
            $offset2 !== null ? $date->copy()->subDays($offset2)->setTime(23, 59) : null,
            $offset3 !== null ? $date->copy()->subDays($offset3)->setTime(23, 59) : null,
        ];
    }

    /**
     * Alternativní název — stejný závod bývá zároveň kolem jiné soutěže
     * (např. krajský přebor i žebříčkový závod zároveň), takže se hodí jiné
     * slovo z $eventNames se stejným místem a rokem. Většina závodů žádný
     * alternativní název nemá.
     *
     * @param list<string> $eventNames
     */
    private static function altName(array $eventNames, string $primaryEventName, string $place, Carbon $date, Generator $faker): ?string
    {
        if (! $faker->boolean(25)) {
            return null;
        }

        $altEventName = $faker->randomElement(array_values(array_diff($eventNames, [$primaryEventName])));

        return $altEventName . ' ' . $place . ' ' . $date->format('Y');
    }
}
