<?php

declare(strict_types=1);

use App\Enums\EntryStatus;
use App\Enums\TransportDirection;
use App\Enums\TransportRequestStatus;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Mail\EventWeeklyEndsBySport;
use App\Mail\PreRaceSummaryMail;
use App\Mail\TransportOfferCancelled;
use App\Mail\TransportRequestCancelled;
use App\Mail\TransportRequestCreated;
use App\Mail\TransportRequestDecided;
use App\Models\AppSetting;
use App\Models\SportClass;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\TransportOffer;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use App\Models\Vehicle;
use App\Services\Mail\MailBranding;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    // The testing DB may hold seeded club settings (e.g. the demo accent); start from none
    AppSetting::query()->delete();
    Cache::flush();
    App::setLocale('cs');
    config()->set('site-config.club.full_name', 'Orientační klub Testov');
    config()->set('site-config.club.abbr', 'TST');
    config()->set('site-config.club.technical_email', 'technik@testov.cz');
});

function clubMailWeeklyEvent(array $attributes = []): SportEvent
{
    return SportEvent::factory()->create([
        'name' => 'Oblastní přebor Testov',
        'oris_id' => null,
        'place' => 'Testov',
        'date' => now()->addDays(20),
        'entry_date_1' => now()->addDays(3)->setTime(23, 59),
        ...$attributes,
    ]);
}

it('builds the branding from club settings', function (): void {
    AppSetting::set(AppSetting::BRANDING_ACCENT_COLOR, '#1F7A5C');
    AppSetting::set(AppSetting::SEO_SAME_AS, ['https://www.facebook.com/testov']);

    $brand = MailBranding::fromSettings();

    expect($brand->clubName)->toBe('Orientační klub Testov')
        ->and($brand->clubInitials)->toBe('TST')
        ->and($brand->accent)->toBe('#1f7a5c')
        ->and($brand->onAccent)->toBe('#ffffff')
        ->and($brand->logoUrl)->toBeNull()
        ->and($brand->contactEmail)->toBe('technik@testov.cz')
        ->and($brand->socialLinks)->toBe([['label' => 'Facebook', 'url' => 'https://www.facebook.com/testov']]);
});

it('falls back to the default accent when none or an invalid one is set', function (): void {
    expect(MailBranding::fromSettings()->accent)->toBe(MailBranding::DEFAULT_ACCENT);

    AppSetting::set(AppSetting::BRANDING_ACCENT_COLOR, 'red');

    expect(MailBranding::fromSettings()->accent)->toBe(MailBranding::DEFAULT_ACCENT);
});

it('renders the weekly summary in the club layout with the club accent', function (): void {
    AppSetting::set(AppSetting::BRANDING_ACCENT_COLOR, '#1f7a5c');
    $event = clubMailWeeklyEvent();

    $mail = new EventWeeklyEndsBySport(collect([$event]), collect(), collect());

    $mail->assertSeeInHtml('Orientační klub Testov')
        ->assertSeeInHtml('#1f7a5c', false)
        ->assertSeeInHtml('Vyber si svůj další závod.')
        ->assertSeeInHtml('První termín přihlášek')
        ->assertSeeInHtml('Nastavení oznámení')
        ->assertDontSeeInHtml('Druhý termín přihlášek')
        ->assertSeeInText('Oblastní přebor Testov')
        ->assertSeeInText('technik@testov.cz');
});

it('lists an event without ORIS id by its name in the weekly summary', function (): void {
    $event = clubMailWeeklyEvent(['name' => 'Klubový trénink bez ORISu', 'alt_name' => null]);

    (new EventWeeklyEndsBySport(collect([$event]), collect(), collect()))
        ->assertSeeInHtml('Klubový trénink bez ORISu')
        ->assertSeeInHtml($event->entry_date_1->format('j. n. · H:i'));
});

it('renders the pre-race summary with runners and course details', function (): void {
    $event = clubMailWeeklyEvent(['start_time' => '10:30:00', 'oris_id' => 9876]);
    $definition = SportClassDefinition::query()->firstOrCreate(
        ['sport_id' => $event->sport_id, 'name' => 'D35'],
        ['age_from' => 35, 'age_to' => 39, 'gender' => 'F'],
    );
    SportClass::factory()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $definition->id,
        'distance' => '4.8',
        'controls' => '12',
        'climbing' => '160',
    ]);
    $profile = UserRaceProfile::query()->create([
        'user_id' => User::factory()->create()->id,
        'first_name' => 'Jana',
        'last_name' => 'Nováková',
        'reg_number' => 'TST8501',
        'gender' => 'F',
        'active' => true,
    ]);
    $entry = UserEntry::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $definition->id,
        'user_race_profile_id' => $profile->id,
        'class_name' => 'D35',
        'requested_start' => '10:42',
        'entry_status' => EntryStatus::Create->value,
        'rent_si' => false,
        'entry_created' => now(),
    ]);

    (new PreRaceSummaryMail($event, collect([$entry])))
        ->assertSeeInHtml('Vše podstatné před startem.')
        ->assertSeeInHtml('Jana Nováková')
        ->assertSeeInHtml('Start 10:42 · +12 min')
        ->assertSeeInHtml('4.8 km · 12 kontrol · ↑ 160 m')
        ->assertSeeInHtml('Zavod?id=9876', false)
        ->assertSeeInText('TST8501');
});

function clubMailTransportRequest(array $attributes = []): TransportRequest
{
    $event = clubMailWeeklyEvent(['date' => '2026-11-14']);
    $driver = User::factory()->create(['name' => 'Petr Dvořák']);
    $offer = TransportOffer::factory()->create([
        'sport_event_id' => $event->id,
        'user_id' => $driver->id,
        'vehicle_id' => Vehicle::factory()->ownedBy($driver)->create(['name' => 'Rodinné kombi'])->id,
        'direction' => TransportDirection::Both,
        'departure_place' => 'Brno, Riviéra',
    ]);

    return TransportRequest::factory()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => User::factory()->create(['name' => 'Romana Klímová'])->id,
        'direction' => TransportDirection::Both,
        'seats' => 2,
        'note' => 'Mám s sebou kolo na střeše.',
        ...$attributes,
    ]);
}

it('renders the car-sharing request with approve button and reject link', function (): void {
    AppSetting::set(AppSetting::BRANDING_ACCENT_COLOR, '#ffd329');
    $request = clubMailTransportRequest();

    (new TransportRequestCreated($request, 'https://example.test/approve', 'https://example.test/reject'))
        ->assertSeeInHtml('Romana Klímová chce jet s tebou.')
        ->assertSeeInHtml('2 místa')
        ->assertSeeInHtml('Rodinné kombi')
        ->assertSeeInHtml('Poznámka od Romana Klímová')
        ->assertSeeInHtml('https://example.test/approve', false)
        ->assertSeeInHtml('club-secondary-negative', false)
        ->assertSeeInText('https://example.test/reject');
});

it('renders the approved car-sharing request with the driver and a link to the transport tab', function (): void {
    $request = clubMailTransportRequest(['status' => TransportRequestStatus::Approved]);
    $event = $request->transportOffer?->sportEvent;

    (new TransportRequestDecided($request))
        ->assertSeeInHtml('Máš místo v autě.')
        ->assertSeeInHtml('Oblastní přebor Testov, 14. listopadu 2026')
        ->assertSeeInHtml('Petr Dvořák')
        ->assertSeeInHtml('Brno, Riviéra')
        ->assertSeeInHtml('Zobrazit dopravu u závodu')
        ->assertSeeInText(SportEventResource::getUrl('entry', ['record' => $event], panel: 'admin'))
        ->assertDontSeeInHtml('Nastavení oznámení');
});

it('renders the rejected car-sharing request with a hint to find other transport', function (): void {
    $request = clubMailTransportRequest(['status' => TransportRequestStatus::Rejected]);

    (new TransportRequestDecided($request))
        ->assertSeeInHtml('Tentokrát to nevyšlo.')
        ->assertSeeInHtml('Najít jinou dopravu')
        ->assertDontSeeInHtml('Máš místo v autě.');
});

it('tells the driver how many seats a cancelled booking freed up', function (): void {
    $request = clubMailTransportRequest();

    (new TransportRequestCancelled($request))
        ->assertSeeInHtml('Místa v autě se uvolnila.')
        ->assertSeeInHtml('Romana Klímová s tebou na závod Oblastní přebor Testov, 14. listopadu 2026 nepojede — 2 místa jsou opět volná.')
        ->assertSeeInHtml('Rodinné kombi')
        ->assertSeeInHtml('Zobrazit moji nabídku');
});

it('still names the race and driver when the cancelled offer was deleted', function (): void {
    $request = clubMailTransportRequest();
    $request->transportOffer?->delete();

    (new TransportOfferCancelled(TransportRequest::query()->findOrFail($request->id)))
        ->assertSeeInHtml('Odvoz na závod se ruší.')
        ->assertSeeInHtml('Oblastní přebor Testov, 14. listopadu 2026')
        ->assertSeeInHtml('Petr Dvořák')
        ->assertSeeInHtml('Najít jinou dopravu');
});

it('renders the car-sharing mails in English', function (): void {
    App::setLocale('en');
    $request = clubMailTransportRequest(['status' => TransportRequestStatus::Approved]);

    (new TransportRequestDecided($request))
        ->assertSeeInHtml('You have a seat in the car.')
        ->assertSeeInHtml('November 14, 2026');

    (new TransportRequestCancelled($request))
        ->assertSeeInHtml('2 seats are free again.');
});
