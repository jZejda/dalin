<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\SportEventType;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\RelayTeam;
use App\Models\UserRaceProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoRelaySeeder extends Seeder
{
    private const int RELAY_DISCIPLINE_ID = 5; // RE

    public function run(): void
    {
        $faker    = \Faker\Factory::create('cs_CZ');
        $profiles = UserRaceProfile::all();

        if ($profiles->count() < 6) {
            return;
        }

        $classDefinitions = SportClassDefinition::whereIn('name', ['H21', 'D21'])->get();

        if ($classDefinitions->isEmpty()) {
            return;
        }

        $clubAbbrs = array_column(DemoClubsSeeder::getData(), 'abbr');

        $events = [
            ['name' => 'Klubové štafety Vysočina',    'place' => 'Vysočina', 'date' => Carbon::now()->subDays(45)->startOfDay()],
            ['name' => 'Oblastní štafety Krkonoše',   'place' => 'Krkonoše', 'date' => Carbon::now()->addDays(30)->startOfDay()],
        ];

        foreach ($events as $eventData) {
            $date = $eventData['date'];
            [$lat, $lon] = DemoSportEventSeeder::coordsFor($eventData['place'], $faker);

            $event = SportEvent::create([
                'name'                 => $eventData['name'] . ' ' . $date->format('Y'),
                'oris_id'              => null,
                'date'                 => $date->toDateString(),
                'place'                => $eventData['place'],
                'gps_lat'              => $lat,
                'gps_lon'              => $lon,
                'sport_id'             => 1,
                'discipline_id'        => self::RELAY_DISCIPLINE_ID,
                'level_id'             => 3,
                'event_type'           => SportEventType::Race->value,
                'use_oris_for_entries' => false,
                'ranking'              => false,
                'entry_date_1'         => $date->copy()->subDays(21)->setTime(23, 59),
                'entry_date_2'         => $date->copy()->subDays(14)->setTime(23, 59),
                'entry_date_3'         => $date->copy()->subDays(7)->setTime(23, 59),
                'cancelled'            => false,
                'organization'         => $faker->randomElements($clubAbbrs, 1),
            ]);

            foreach ($classDefinitions as $definition) {
                $sportClass = SportClass::factory()->relay(3)->create([
                    'sport_event_id'      => $event->id,
                    'class_definition_id' => $definition->id,
                    'name'                => $definition->name,
                    'fee'                 => 450.0,
                ]);

                foreach (['A', 'B'] as $suffix) {
                    $team = RelayTeam::create([
                        'sport_event_id' => $event->id,
                        'sport_class_id' => $sportClass->id,
                        'name'           => 'Demo ' . $definition->name . ' ' . $suffix,
                        'relay_type'     => 'ST',
                        'slots_count'    => 3,
                    ]);

                    $team->syncMemberSlots();

                    $runnerIds = $profiles->random(3)->pluck('id')->all();

                    foreach ($team->members()->orderBy('slot')->get() as $member) {
                        $runnerId = array_shift($runnerIds);

                        if ($runnerId === null) {
                            break;
                        }

                        $member->update([
                            'user_race_profile_id' => $runnerId,
                        ]);
                    }
                }
            }
        }
    }
}
