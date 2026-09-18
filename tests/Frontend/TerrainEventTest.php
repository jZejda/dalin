<?php

declare(strict_types=1);

use App\Enums\SportEventLinkType;
use App\Enums\SportEventMarkerType;
use App\Enums\SportEventTransportType;
use App\Enums\SportEventType;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

uses(DatabaseTransactions::class);

beforeEach(function () {
    $this->withoutVite();
    $this->travelTo(now()->setDate(2026, 9, 18)->setTime(12, 0));
    $this->event = SportEvent::factory()->createQuietly([
        'name' => 'Terrain testovací závod',
        'date' => '2026-10-15',
        'date_end' => '2026-10-16',
        'event_type' => SportEventType::Race,
        'cancelled' => false,
        'discipline_id' => null,
        'level_id' => null,
        'stages' => null,
        'weather' => null,
        'gps_lat' => null,
        'gps_lon' => null,
        'transport_type' => SportEventTransportType::None,
        'entry_date_1' => '2026-09-10 23:59:00',
        'entry_date_2' => '2026-09-25 23:59:00',
        'entry_date_3' => null,
        'increase_entry_fee_2' => 100,
        'event_info' => '<p>Informace pro účastníky <a href="/stranka/o-klubu">klubu</a>.</p>',
        'event_warning' => null,
        'entry_desc' => '<p>Pokyny k přihlášení.</p>',
        'organization' => ['PBM'],
        'region' => ['JM'],
        'oris_id' => 9858,
        'place' => 'Lovčičky',
    ]);
});

it('preserves event information, date range, oris and successive entry deadlines', function () {
    $this->get(route('sport-event.show', $this->event->id))
        ->assertOk()
        ->assertSee('Terrain testovací závod')
        ->assertSee('Lovčičky')
        ->assertSee('PBM')
        ->assertSee('ORIS 9858')
        ->assertSee('2. termín')
        ->assertSee('+100 Kč')
        ->assertSee('Pokyny k přihlášení.')
        ->assertSee('<a href="/stranka/o-klubu">klubu</a>', false);
});

it('shows cancellation and the reason without losing event information', function () {
    $this->event->updateQuietly(['cancelled' => true, 'cancelled_reason' => 'Nesjízdné cesty']);
    $this->get(route('sport-event.show', $this->event->id))
        ->assertOk()->assertSee('Závod byl zrušen')->assertSee('Nesjízdné cesty')
        ->assertDontSee('data-event-entry', false);
});

it('distinguishes passed entry deadlines from missing ones', function () {
    $this->event->updateQuietly(['entry_date_2' => '2026-09-15 23:59:00']);
    $this->get(route('sport-event.show', $this->event->id))->assertOk()->assertSee('Po uzávěrce');

    $this->event->updateQuietly(['entry_date_1' => null, 'entry_date_2' => null]);
    $this->get(route('sport-event.show', $this->event->id))->assertOk()->assertSee('Nevypsána');
});

it('omits the map and optional sections when their data is absent', function () {
    $this->get(route('sport-event.show', $this->event->id))
        ->assertOk()->assertDontSee('id="map"', false)
        ->assertDontSee(__('sport-event.public.links_title'))
        ->assertDontSee(__('sport-event.public.services_title'))
        ->assertDontSee(__('sport-event.public.weather_title'));
});

it('returns 404 for a missing event', function () {
    $this->get('/akce/999999999')->assertNotFound();
});

it('offers the existing entry page for guests and signed-in users without exposing participant identities', function () {
    $url = SportEventResource::getUrl('entry', ['record' => $this->event->id], panel: 'admin');
    $this->get(route('sport-event.show', $this->event->id))
        ->assertOk()->assertSee($url)->assertSee(__('sport-event.public.login_for_entries'))
        ->assertSee('data-terrain-theme', false);

    $user = User::factory()->create();
    $this->actingAs($user)->get(route('sport-event.show', $this->event->id))
        ->assertOk()->assertSee($url)->assertSee(__('sport-event.public.manage_entries'));
});

it('preserves documents, categories, services, news, warnings, weather and map markers', function () {
    $this->event->updateQuietly([
        'gps_lat' => '49.117', 'gps_lon' => '16.847',
        'transport_type' => SportEventTransportType::SelfOnly,
        'event_warning' => '<p>Parkujte pouze na vyznačených místech.</p>',
        'weather' => ['main' => ['temp' => 18.5], 'weather' => [['id' => 800, 'description' => 'Jasno']]],
    ]);
    $this->event->sportEventLinks()->create([
        'source_url' => 'https://example.org/rozpis.pdf', 'source_type' => SportEventLinkType::Invitation,
    ]);
    $definition = SportClassDefinition::create(['sport_id' => $this->event->sport_id, 'name' => 'H21 Terrain', 'age_from' => 18, 'age_to' => 40, 'gender' => 'M']);
    $this->event->sportClasses()->create(['class_definition_id' => $definition->id, 'name' => 'H21 Terrain']);
    $this->event->sportServices()->create([
        'service_name_cz' => 'Parkování Terrain', 'last_booking_date_time' => '2026-10-10 23:59:00',
        'unit_price' => 50, 'qty_available' => 100, 'qty_remaining' => 0,
    ]);
    $this->event->sportEventNews()->create(['date' => '2026-09-17', 'text' => '<p>Nové informace Terrain.</p>']);
    $this->event->sportEventMarkers()->create([
        'label' => 'Parkoviště Terrain', 'letter' => 'P', 'type' => SportEventMarkerType::Parking,
        'lat' => 49.118, 'lon' => 16.848, 'desc' => 'U lesa',
    ]);

    $this->get(route('sport-event.show', $this->event->id))
        ->assertOk()->assertSee('https://example.org/rozpis.pdf', false)
        ->assertSee('id="event-documents"', false)
        ->assertSee('H21 Terrain')->assertSee('Parkování Terrain')->assertSee('50 Kč')
        ->assertSee('Nové informace Terrain.')->assertSee('Parkujte pouze na vyznačených místech.')
        ->assertSee('Jasno')->assertSee('18.5')
        ->assertSee('id="map"', false)->assertSee('Parkoviště Terrain')->assertSee('U lesa')
        ->assertSee(__('sport-event.public.transport_no_offers'));
});
