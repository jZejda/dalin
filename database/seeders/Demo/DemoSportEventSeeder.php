<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\SportEventType;
use App\Models\SportEvent;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoSportEventSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('cs_CZ');

        $clubAbbrs = array_column(DemoClubsSeeder::getData(), 'abbr');

        $czechPlaces = [
            'Brno', 'Praha', 'Plzeň', 'Liberec', 'Olomouc', 'Ostrava', 'Hradec Králové',
            'České Budějovice', 'Pardubice', 'Zlín', 'Jihlava', 'Kladno', 'Tábor', 'Chomutov',
            'Znojmo', 'Příbram', 'Písek', 'Prachatice', 'Strakonice', 'Třeboň', 'Český Krumlov',
            'Šumava', 'Beskydy', 'Jeseníky', 'Krkonoše', 'Vysočina', 'Podyjí', 'Slavkov u Brna',
            'Mikulov', 'Lednice', 'Valtice', 'Pálava', 'Blansko', 'Vyškov', 'Hodonín',
        ];

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
            $place   = $faker->randomElement($czechPlaces);
            $name    = $faker->randomElement($eventNames) . ' ' . $place . ' ' . $date->format('Y');

            SportEvent::create([
                'name'                => $name,
                'oris_id'             => null,
                'date'                => $date->toDateString(),
                'place'               => $place,
                'sport_id'            => 1,
                'discipline_id'       => $faker->randomElement($disciplines),
                'level_id'            => $faker->randomElement($levels),
                'event_type'          => $faker->randomElement($types),
                'use_oris_for_entries' => false,
                'ranking'             => $faker->boolean(60),
                'ranking_coefficient' => $faker->randomFloat(1, 0.5, 1.5),
                'entry_date_1'        => $date->copy()->subDays(21)->setTime(23, 59),
                'entry_date_2'        => $date->copy()->subDays(14)->setTime(23, 59),
                'entry_date_3'        => $date->copy()->subDays(7)->setTime(23, 59),
                'cancelled'           => $faker->boolean(5),
                'organization'        => $faker->randomElements($clubAbbrs, $faker->numberBetween(1, 2)),
            ]);
        }

        // 15 future events
        for ($i = 0; $i < 15; $i++) {
            $daysAhead = $faker->numberBetween(7, 180);
            $date      = Carbon::now()->addDays($daysAhead)->startOfDay();
            $place     = $faker->randomElement($czechPlaces);
            $name      = $faker->randomElement($eventNames) . ' ' . $place . ' ' . $date->format('Y');

            SportEvent::create([
                'name'                => $name,
                'oris_id'             => null,
                'date'                => $date->toDateString(),
                'place'               => $place,
                'sport_id'            => 1,
                'discipline_id'       => $faker->randomElement($disciplines),
                'level_id'            => $faker->randomElement($levels),
                'event_type'          => SportEventType::Race->value,
                'use_oris_for_entries' => false,
                'ranking'             => $faker->boolean(70),
                'ranking_coefficient' => $faker->randomFloat(1, 0.5, 1.5),
                'entry_date_1'        => $date->copy()->subDays(21)->setTime(23, 59),
                'entry_date_2'        => $date->copy()->subDays(14)->setTime(23, 59),
                'entry_date_3'        => $date->copy()->subDays(7)->setTime(23, 59),
                'cancelled'           => false,
                'organization'        => $faker->randomElements($clubAbbrs, $faker->numberBetween(1, 2)),
            ]);
        }
    }
}
