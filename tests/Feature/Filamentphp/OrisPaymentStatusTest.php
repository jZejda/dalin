<?php

declare(strict_types=1);

use App\Enums\AppRoles;
use App\Filament\Resources\SportEvents\Pages\ViewSportEvent;
use App\Filament\Resources\SportEvents\Widgets\OrisPaymentStatus;
use App\Models\SportEvent;
use App\Models\User;
use App\Services\OrisApiService;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    config(['site-config.club.abbr' => 'ABM']);
    Cache::flush();
});

/**
 * @param array<string, array<string, int|string>> $clubs
 */
function fakeOrisBalance(array $clubs, string $status = 'OK'): void
{
    Http::fake([
        '*' => Http::response([
            'Method' => 'getEventBalance',
            'Format' => 'json',
            'Status' => $status,
            'ExportCreated' => now()->toDateTimeString(),
            'Data' => ['EventID' => '2077', 'Currency' => 'CZK', 'Clubs' => $clubs],
        ]),
    ]);
}

/**
 * @return array<string, int|string>
 */
function orisClubRow(string $abbr, int $total, int $paid): array
{
    return [
        'ClubID' => '7', 'ClubAbbr' => $abbr,
        'FeeEntry' => $total - 40, 'FeeSI' => 40, 'FeeService' => 0,
        'FeeTotal' => $total, 'Paid' => $paid, 'ToBePaid' => $total - $paid,
        'PaymentVS' => '2631117',
    ];
}

it('reads the own club row from ORIS getEventBalance', function (): void {
    fakeOrisBalance([
        'Club_1' => orisClubRow('AOV', 950, 750),
        'Club_2' => orisClubRow('ABM', 1690, 1690),
    ]);

    $balance = (new OrisApiService())->getEventClubBalance(2077, 'abm');

    expect($balance)->not->toBeNull()
        ->and($balance?->feeTotal)->toBe(1690.0)
        ->and($balance?->feeSi)->toBe(40.0)
        ->and($balance?->paid)->toBe(1690.0)
        ->and($balance?->paymentVs)->toBe('2631117')
        ->and($balance?->isFullyPaid())->toBeTrue();

    Http::assertSent(fn ($request): bool => $request['method'] === 'getEventBalance' && (int) $request['eventid'] === 2077);
});

it('returns null when the club has no row in the settlement', function (): void {
    fakeOrisBalance(['Club_1' => orisClubRow('AOV', 950, 750)]);

    expect((new OrisApiService())->getEventClubBalance(2077, 'ABM'))->toBeNull();
});

it('throws when ORIS does not answer with status OK', function (): void {
    fakeOrisBalance([], status: 'Error');

    (new OrisApiService())->getEventClubBalance(2077, 'ABM');
})->throws(HttpClientException::class);

it('shows the remaining amount of an unpaid settlement', function (): void {
    actingAsSuperAdmin();
    fakeOrisBalance(['Club_2' => orisClubRow('ABM', 1690, 1000)]);
    $event = SportEvent::factory()->create(['oris_id' => 2077]);

    livewire(OrisPaymentStatus::class, ['record' => $event])
        ->assertSuccessful()
        ->assertSee(__('sport-event.widgets.oris_payment.heading'))
        ->assertSee(__('sport-event.widgets.oris_payment.status_unpaid'))
        ->assertSeeHtml("690\u{00A0}Kč")
        ->assertSee(__('sport-event.widgets.oris_payment.payment_vs', ['vs' => '2631117']));
});

it('shows a fully paid settlement and caches the ORIS answer', function (): void {
    actingAsSuperAdmin();
    fakeOrisBalance(['Club_2' => orisClubRow('ABM', 1690, 1690)]);
    $event = SportEvent::factory()->create(['oris_id' => 2077]);

    livewire(OrisPaymentStatus::class, ['record' => $event])
        ->assertSee(__('sport-event.widgets.oris_payment.status_paid'));
    livewire(OrisPaymentStatus::class, ['record' => $event])
        ->assertSee(__('sport-event.widgets.oris_payment.status_paid'));

    Http::assertSentCount(1);
});

it('explains when the club has no entries in the event', function (): void {
    actingAsSuperAdmin();
    fakeOrisBalance(['Club_1' => orisClubRow('AOV', 950, 750)]);
    $event = SportEvent::factory()->create(['oris_id' => 2077]);

    livewire(OrisPaymentStatus::class, ['record' => $event])
        ->assertSee(__('sport-event.widgets.oris_payment.no_entries'));
});

it('explains when ORIS is unavailable and does not cache the failure', function (): void {
    actingAsSuperAdmin();
    Http::fake(['*' => Http::response('', 500)]);
    $event = SportEvent::factory()->create(['oris_id' => 2077]);

    livewire(OrisPaymentStatus::class, ['record' => $event])
        ->assertSee(__('sport-event.widgets.oris_payment.unavailable'));

    expect(Cache::has(OrisPaymentStatus::cacheKey(2077)))->toBeFalse();
});

it('is hidden from plain members and on events without an ORIS id', function (): void {
    Role::firstOrCreate(['name' => AppRoles::Member->value, 'guard_name' => 'web']);
    $member = User::factory()->create();
    $member->assignRole(AppRoles::Member->value);
    $this->actingAs($member);

    expect(OrisPaymentStatus::canView())->toBeFalse();

    actingAsSuperAdmin();
    expect(OrisPaymentStatus::canView())->toBeTrue();

    $withoutOris = SportEvent::factory()->create(['oris_id' => null]);
    livewire(ViewSportEvent::class, ['record' => $withoutOris->getRouteKey()])
        ->assertSuccessful()
        ->assertDontSee(__('sport-event.widgets.oris_payment.heading'));
});
