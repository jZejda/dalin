<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\SportEventLinkType;
use App\Enums\SportEventMarkerType;
use App\Enums\SportEventType;
use App\Models\SportEvent;
use App\Models\SportEventExport;
use App\Models\SportEventLink;
use App\Models\SportEventMarker;
use App\Models\SportEventNews;
use Faker\Generator;
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

        // Map markers laid out around the real coordinates of each event —
        // all upcoming races plus the recent past, so the map is never empty
        $mappedEvents = SportEvent::where('date', '>=', Carbon::today()->subMonths(3))
            ->orderBy('date')
            ->get();

        foreach ($mappedEvents as $event) {
            $this->seedMarkers($event, $faker);
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

    /**
     * Parkoviště, centrum a start rozmístěné kolem souřadnic závodu tak,
     * jak bývají ve skutečnosti — pár set metrů od sebe.
     */
    private function seedMarkers(SportEvent $event, Generator $faker): void
    {
        $lat = (float) $event->gps_lat;
        $lon = (float) $event->gps_lon;

        if ($lat === 0.0 || $lon === 0.0) {
            return;
        }

        [$parkLat, $parkLon] = $this->offset($lat, $lon, $faker->numberBetween(200, 700), $faker->numberBetween(0, 359));

        SportEventMarker::factory()->ofType(SportEventMarkerType::Parking)->create([
            'sport_event_id' => $event->id,
            'letter'         => 'P',
            'label'          => 'Parkoviště',
            'desc'           => 'Parkujte podle pokynů pořadatele.',
            'lat'            => $parkLat,
            'lon'            => $parkLon,
        ]);

        SportEventMarker::factory()->ofType(
            $event->event_type === SportEventType::Training
                ? SportEventMarkerType::Training
                : SportEventMarkerType::ObRaceSimple
        )->create([
            'sport_event_id' => $event->id,
            'letter'         => 'C',
            'label'          => 'Centrum závodu',
            'desc'           => 'Shromaždiště, prezentace a cíl.',
            'lat'            => number_format($lat, 6, '.', ''),
            'lon'            => number_format($lon, 6, '.', ''),
        ]);

        if ($faker->boolean(70)) {
            [$startLat, $startLon] = $this->offset($lat, $lon, $faker->numberBetween(400, 1500), $faker->numberBetween(0, 359));

            SportEventMarker::factory()->ofType(SportEventMarkerType::StageStart)->create([
                'sport_event_id' => $event->id,
                'letter'         => 'S',
                'label'          => 'Start',
                'desc'           => 'Vzdálenost od centra po modrobílých fáborcích.',
                'lat'            => $startLat,
                'lon'            => $startLon,
            ]);
        }
    }

    /**
     * Posune souřadnice o zadanou vzdálenost v metrech daným azimutem.
     *
     * @return array{0: string, 1: string}
     */
    private function offset(float $lat, float $lon, int $metres, int $bearing): array
    {
        $rad = deg2rad((float) $bearing);

        $dLat = ($metres * cos($rad)) / 111_320;
        $dLon = ($metres * sin($rad)) / (111_320 * cos(deg2rad($lat)));

        return [
            number_format($lat + $dLat, 6, '.', ''),
            number_format($lon + $dLon, 6, '.', ''),
        ];
    }
}
