<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\EntryStatus;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoUserEntrySeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('cs_CZ');

        $raceProfiles     = UserRaceProfile::all();
        $classDefinitions = SportClassDefinition::all();

        if ($raceProfiles->isEmpty() || $classDefinitions->isEmpty()) {
            return;
        }

        $pastEvents   = SportEvent::where('date', '<', Carbon::today())->get();
        $futureEvents = SportEvent::where('date', '>=', Carbon::today())->get();

        // Past events: 8–15 entries each
        foreach ($pastEvents as $event) {
            $count    = $faker->numberBetween(8, 15);
            $profiles = $raceProfiles->random(min($count, $raceProfiles->count()));

            foreach ($profiles as $profile) {
                $classdef = $classDefinitions->random();

                UserEntry::create([
                    'sport_event_id'       => $event->id,
                    'class_definition_id'  => $classdef->id,
                    'user_race_profile_id' => $profile->id,
                    'class_name'           => $classdef->name,
                    'entry_status'         => EntryStatus::Create->value,
                    'entry_lock'           => false,
                    'rent_si'              => $faker->boolean(10),
                    'entry_created'        => $event->entry_date_1 ?? Carbon::now()->subDays(25),
                ]);
            }
        }

        // Future events: 5–10 entries each
        foreach ($futureEvents as $event) {
            $count    = $faker->numberBetween(5, 10);
            $profiles = $raceProfiles->random(min($count, $raceProfiles->count()));

            foreach ($profiles as $profile) {
                $classdef = $classDefinitions->random();

                UserEntry::create([
                    'sport_event_id'       => $event->id,
                    'class_definition_id'  => $classdef->id,
                    'user_race_profile_id' => $profile->id,
                    'class_name'           => $classdef->name,
                    'entry_status'         => $faker->randomElement([
                        EntryStatus::Create->value,
                        EntryStatus::Edit->value,
                    ]),
                    'entry_lock'           => false,
                    'rent_si'              => $faker->boolean(10),
                    'entry_created'        => Carbon::now()->subDays($faker->numberBetween(1, 14)),
                ]);
            }
        }
    }
}
