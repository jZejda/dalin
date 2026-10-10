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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DemoSportEventExtrasSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('cs_CZ');

        $pastEvents   = SportEvent::where('date', '<', Carbon::today())->orderByDesc('date')->take(6)->get();
        $futureEvents = SportEvent::where('date', '>=', Carbon::today())->orderBy('date')->take(6)->get();

        // Links: invitations for upcoming, results for finished events; the URLs mimic the
        // real sources (ORIS, OResults, Rajče, …) so every link-source icon shows up
        foreach ($futureEvents as $event) {
            SportEventLink::factory()->ofType(SportEventLinkType::Invitation)->create([
                'sport_event_id' => $event->id,
                'name_cz'        => 'Rozpis závodu',
                'source_url'     => 'https://oris.orientacnisporty.cz/files/' . $event->id . '_demo_rozpis.pdf',
            ]);

            if ($faker->boolean(50)) {
                SportEventLink::factory()->ofType(SportEventLinkType::EventWebsite)->create([
                    'sport_event_id' => $event->id,
                    'name_cz'        => 'Web závodu',
                    'source_url'     => 'https://example.org/zavod/' . $event->id,
                ]);
            }
        }

        // A file uploaded straight to DaLin (shows the DaLin source icon)
        if ($uploadEvent = $futureEvents->first()) {
            $path = SportEventLink::FILE_DIRECTORY . '/' . $uploadEvent->id . '/pokyny-pro-zavodniky.txt';
            Storage::disk(SportEventLink::FILE_DISK)->put($path, implode(PHP_EOL, [
                'Pokyny pro závodníky – ' . $uploadEvent->name,
                '',
                'Prezentace: v centru závodu od 8:30, start 00 v 10:00.',
                'Parkování: na louce u lesa, dodržujte pokyny pořadatelů.',
                'Vzdálenosti: parkoviště – centrum 300 m, centrum – start 1,2 km.',
                'Mapa: 1:10 000, E 5 m, stav jaro ' . now()->year . '.',
            ]));

            SportEventLink::factory()->ofType(SportEventLinkType::Information)->create([
                'sport_event_id' => $uploadEvent->id,
                'name_cz'        => 'Pokyny pro závodníky',
                'name_en'        => 'Final information',
                'source_url'     => null,
                'source_path'    => $path,
                'internal'       => true,
            ]);
        }

        foreach ($pastEvents as $event) {
            SportEventLink::factory()->ofType(SportEventLinkType::Results)->create([
                'sport_event_id' => $event->id,
                'name_cz'        => 'Oficiální výsledky',
                'source_url'     => 'https://oresults.eu/events/' . $event->id,
            ]);

            SportEventLink::factory()->ofType(SportEventLinkType::Photos)->create([
                'sport_event_id' => $event->id,
                'name_cz'        => 'Fotogalerie',
                'source_url'     => $faker->randomElement([
                    'https://demo-klub.rajce.idnes.cz/zavod-' . $event->id,
                    'https://photos.app.goo.gl/demo' . $event->id,
                ]),
            ]);

            if ($faker->boolean(50)) {
                SportEventLink::factory()->ofType(SportEventLinkType::RouteChoices)->create([
                    'sport_event_id' => $event->id,
                    'name_cz'        => 'Postupy',
                    'source_url'     => 'https://www.livelox.com/Viewer/Event?eventId=' . $event->id,
                ]);
            }
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

        // Export definitions listed in the admin panel; the first one also gets a published
        // IOF start list so /startovka/{slug} (incl. its SEO) can be showcased
        foreach ($futureEvents->take(2) as $index => $event) {
            $slug = Str::slug('startovka-' . $event->id . '-' . $event->place);
            $path = Carbon::parse($event->date)->format('Y') . '/' . $slug . '.xml';

            SportEventExport::factory()->create([
                'title'          => 'Startovka – ' . $event->name,
                'slug'           => $slug,
                'sport_event_id' => $event->id,
                'start_time'     => Carbon::parse($event->date)->setTime(10, 0),
                'result_path'    => $path,
            ]);

            if ($index === 0) {
                Storage::disk('events')->put($path, $this->startListXml($event, $faker));
            }
        }
    }

    /**
     * IOF XML 3.0 start list: a few classes, Czech runners from demo clubs, two-minute
     * intervals and one vacancy per class, as an organizer's software would export it.
     */
    private function startListXml(SportEvent $event, Generator $faker): string
    {
        $clubs = DemoClubsSeeder::getData();
        $date = Carbon::parse($event->date)->format('Y-m-d');

        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('StartList');
        $xml->writeAttribute('xmlns', 'http://www.orienteering.org/datastandard/3.0');
        $xml->writeAttribute('iofVersion', '3.0');
        $xml->writeAttribute('createTime', Carbon::parse($event->date)->subDays(3)->setTime(19, 12)->format('Y-m-d\TH:i:s'));
        $xml->writeAttribute('creator', 'QuickEvent 3.1');
        $xml->startElement('Event');
        $xml->writeElement('Name', $event->name);
        $xml->endElement();

        $classes = ['H21' => [6800, 210, 22], 'D21' => [5400, 170, 18], 'H35' => [5900, 190, 19], 'D14' => [2900, 80, 11]];

        foreach (array_keys($classes) as $classIndex => $className) {
            [$length, $climb, $controls] = $classes[$className];
            $female = str_starts_with($className, 'D');

            $xml->startElement('ClassStart');
            $xml->startElement('Class');
            $xml->writeElement('Id', (string) ($classIndex + 1));
            $xml->writeElement('Name', $className);
            $xml->endElement();
            $xml->startElement('Course');
            $xml->writeElement('Name', $className);
            $xml->writeElement('Length', (string) $length);
            $xml->writeElement('Climb', (string) $climb);
            $xml->writeElement('NumberOfControls', (string) $controls);
            $xml->endElement();

            $runners = $faker->numberBetween(5, 9);
            for ($i = 0; $i <= $runners; $i++) {
                $vacancy = $i === $runners;
                $club = $faker->randomElement($clubs);
                $startTime = Carbon::parse($date . ' 10:00:00')->addMinutes(2 * $i + $classIndex);

                $xml->startElement('PersonStart');
                $xml->startElement('Person');
                if (! $vacancy) {
                    $xml->writeElement('Id', $club['abbr'] . $faker->numberBetween(5000, 9999));
                }
                $xml->startElement('Name');
                $xml->writeElement('Family', $vacancy ? 'Vakant' : $faker->lastName($female ? 'female' : 'male'));
                $xml->writeElement('Given', $vacancy ? 'Vakant' : $faker->firstName($female ? 'female' : 'male'));
                $xml->endElement();
                $xml->endElement();
                $xml->startElement('Organisation');
                $xml->writeElement('Id', $vacancy ? '0' : $club['oris_id']);
                $xml->writeElement('Name', $vacancy ? 'Vakant' : $club['name']);
                $xml->writeElement('ShortName', $vacancy ? 'Vakant' : $club['abbr']);
                $xml->endElement();
                $xml->startElement('Start');
                $xml->writeElement('StartTime', $startTime->format('Y-m-d\TH:i:s'));
                $xml->writeElement('ControlCard', (string) $faker->numberBetween(2000000, 8999999));
                $xml->endElement();
                $xml->endElement();
            }

            $xml->endElement();
        }

        $xml->endElement();
        $xml->endDocument();

        return $xml->outputMemory();
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

            if ($event->stages !== null && $event->stages > 1) {
                [$endLat, $endLon] = $this->offset($lat, $lon, $faker->numberBetween(400, 1500), $faker->numberBetween(0, 359));

                SportEventMarker::factory()->ofType(SportEventMarkerType::StageEnd)->create([
                    'sport_event_id' => $event->id,
                    'letter'         => 'F',
                    'label'          => 'Cíl etapy',
                    'desc'           => 'Cíl etapy s časomírou.',
                    'lat'            => $endLat,
                    'lon'            => $endLon,
                ]);
            }
        }

        if ($event->event_type === SportEventType::TrainingCamp) {
            [$accommodationLat, $accommodationLon] = $this->offset($lat, $lon, $faker->numberBetween(100, 500), $faker->numberBetween(0, 359));

            SportEventMarker::factory()->ofType(SportEventMarkerType::Accommodation)->create([
                'sport_event_id' => $event->id,
                'letter'         => 'U',
                'label'          => 'Ubytování',
                'desc'           => 'Chata s kapacitou pro celý oddíl.',
                'lat'            => $accommodationLat,
                'lon'            => $accommodationLon,
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
