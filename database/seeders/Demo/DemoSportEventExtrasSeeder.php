<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\SportEventLinkType;
use App\Enums\SportEventMarkerType;
use App\Models\SportEvent;
use App\Models\SportEventExport;
use App\Models\SportEventLink;
use App\Models\SportEventMarker;
use App\Models\SportEventNews;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DemoSportEventExtrasSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('cs_CZ');

        $pastEvents   = SportEvent::where('date', '<', Carbon::today())->orderByDesc('date')->take(6)->get();
        $futureEvents = SportEvent::where('date', '>=', Carbon::today())->orderBy('date')->take(6)->get();

        // Links: invitations for upcoming, results for finished events
        foreach ($futureEvents as $event) {
            SportEventLink::factory()->ofType(SportEventLinkType::Invitation)->create([
                'sport_event_id' => $event->id,
                'name_cz'        => 'Rozpis závodu',
                'source_url'     => 'https://example.org/rozpis/' . $event->id,
            ]);

            if ($faker->boolean(50)) {
                SportEventLink::factory()->ofType(SportEventLinkType::EventWebsite)->create([
                    'sport_event_id' => $event->id,
                    'name_cz'        => 'Web závodu',
                    'source_url'     => 'https://example.org/zavod/' . $event->id,
                ]);
            }
        }

        foreach ($pastEvents as $event) {
            SportEventLink::factory()->ofType(SportEventLinkType::Results)->create([
                'sport_event_id' => $event->id,
                'name_cz'        => 'Oficiální výsledky',
                'source_url'     => 'https://example.org/vysledky/' . $event->id,
            ]);
        }

        // Map markers (parking + event centre) for upcoming events
        foreach ($futureEvents as $event) {
            $lat = $faker->randomFloat(6, 48.8, 50.8);
            $lon = $faker->randomFloat(6, 12.5, 18.5);

            SportEventMarker::factory()->ofType(SportEventMarkerType::Parking)->create([
                'sport_event_id' => $event->id,
                'letter'         => 'P',
                'label'          => 'Parkoviště',
                'desc'           => 'Parkujte podle pokynů pořadatele.',
                'lat'            => $lat,
                'lon'            => $lon,
            ]);

            SportEventMarker::factory()->create([
                'sport_event_id' => $event->id,
                'letter'         => 'C',
                'label'          => 'Centrum závodu',
                'desc'           => 'Shromaždiště, prezentace a cíl.',
                'lat'            => $lat + 0.01,
                'lon'            => $lon + 0.01,
            ]);
        }

        // News feed items
        $newsTexts = [
            'Zveřejněny startovní listiny nadcházejícího závodu.',
            'Pozor, změna místa shromaždiště – sledujte pokyny.',
            'Doplněny výsledky a mezičasy z víkendového závodu.',
            'Uzávěrka přihlášek se blíží, nezapomeňte se přihlásit.',
            'Pořadatel doplnil informace o parkování a dopravě.',
        ];

        foreach ($newsTexts as $index => $text) {
            SportEventNews::factory()->create([
                'sport_event_id' => $futureEvents->random()->id,
                'text'           => $text,
                'date'           => Carbon::now()->subDays($index * 3 + 1)->toDateString(),
            ]);
        }

        // Export definitions listed in the admin panel (no generated files in demo)
        foreach ($futureEvents->take(2) as $event) {
            SportEventExport::factory()->create([
                'title'          => 'Startovka – ' . $event->name,
                'slug'           => Str::slug('startovka-' . $event->id . '-' . $event->place),
                'sport_event_id' => $event->id,
                'start_time'     => Carbon::parse($event->date)->setTime(10, 0),
            ]);
        }
    }
}
