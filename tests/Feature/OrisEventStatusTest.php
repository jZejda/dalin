<?php

declare(strict_types=1);

use App\Enums\AppRoles;
use App\Enums\EntryStatus;
use App\Enums\OrisPaymentState;
use App\Http\Controllers\Cron\Jobs\SyncOrisEventStatuses;
use App\Livewire\SportEvent\OrisEventHeader;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\SportEventOrisStatus;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use App\Services\SportEvents\OrisEventStatusRefresher;
use Illuminate\Http\Client\HttpClientException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;

use function Pest\Livewire\livewire;

beforeEach(function (): void {
    config(['site-config.club.abbr' => 'ABM', 'demo.enabled' => false]);
    Http::preventStrayRequests();
});

/**
 * Fakes ORIS getEvent and getEventBalance; $clubRow null = the club has no row in the settlement.
 *
 * @param array<string, int|string>|null $clubRow
 */
function fakeOrisEventStatus(?array $clubRow, string $bankAccount = '2901296231/2010', int $starts = 0, int $results = 0): void
{
    Http::fake(function (Request $request) use ($clubRow, $bankAccount, $starts, $results) {
        $data = match ($request['method']) {
            'getEvent' => [
                'ID' => (string) $request['id'],
                'EntryBankAccount' => $bankAccount,
                'CurrentStartsCount' => $starts,
                'CurrentResultsCount' => $results,
            ],
            'getEventBalance' => [
                'EventID' => (string) $request['eventid'],
                'Currency' => 'CZK',
                'Clubs' => array_filter([
                    'Club_7' => ['ClubID' => '7', 'ClubAbbr' => 'AOV', 'FeeEntry' => 900, 'FeeSI' => 0, 'FeeService' => 0, 'FeeTotal' => 900, 'Paid' => 900, 'ToBePaid' => 0],
                    'Club_1' => $clubRow,
                ]),
            ],
        };

        return Http::response(['Method' => $request['method'], 'Format' => 'json', 'Status' => 'OK', 'ExportCreated' => now()->toDateTimeString(), 'Data' => $data]);
    });
}

/**
 * @return array<string, int|string>
 */
function orisClubBalanceRow(int $entry, int $service, int $paid): array
{
    return [
        'ClubID' => '1', 'ClubAbbr' => 'ABM',
        'FeeEntry' => $entry, 'FeeSI' => 40, 'FeeService' => $service,
        'FeeTotal' => $entry + 40 + $service, 'Paid' => $paid, 'ToBePaid' => $entry + 40 + $service - $paid,
        'PaymentVS' => '2631104',
    ];
}

function orisStatusEntry(SportEvent $event, EntryStatus $status = EntryStatus::Create): UserEntry
{
    $definition = SportClassDefinition::query()->firstOrCreate(
        ['sport_id' => $event->sport_id, 'name' => 'H21'],
        ['age_from' => 21, 'age_to' => 34, 'gender' => 'M'],
    );

    return UserEntry::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $definition->id,
        'user_race_profile_id' => UserRaceProfile::factory()->create()->id,
        'class_name' => 'H21',
        'entry_status' => $status->value,
        'rent_si' => false,
        'entry_created' => now(),
    ]);
}

function orisStatusUser(AppRoles $role): User
{
    Role::firstOrCreate(['name' => $role->value, 'guard_name' => 'web']);
    $user = User::factory()->create(['active' => true]);
    $user->assignRole($role->value);

    return $user;
}

it('derives the payment state from the ORIS amounts', function (float $paid, float $toBePaid, OrisPaymentState $state): void {
    expect(OrisPaymentState::fromAmounts($paid, $toBePaid))->toBe($state);
})->with([
    'nothing paid' => [0, 560, OrisPaymentState::Unpaid],
    'second wave due' => [520, 160, OrisPaymentState::PartiallyPaid],
    'settled' => [680, 0, OrisPaymentState::Paid],
    'overpaid' => [700, -20, OrisPaymentState::Overpaid],
]);

it('stores the bank account, publication counts and club settlement from ORIS', function (): void {
    fakeOrisEventStatus(orisClubBalanceRow(entry: 520, service: 120, paid: 520), starts: 812);
    $event = SportEvent::factory()->create(['oris_id' => 109642]);

    $status = (new OrisEventStatusRefresher())->refresh($event);

    expect($status->bank_account)->toBe('2901296231/2010')
        ->and($status->starts_count)->toBe(812)
        ->and($status->hasStartList())->toBeTrue()
        ->and($status->hasResults())->toBeFalse()
        ->and($status->fee_total)->toBe(680.0)
        ->and($status->fee_service)->toBe(120.0)
        ->and($status->to_be_paid)->toBe(160.0)
        ->and($status->payment_vs)->toBe('2631104')
        ->and($status->paymentState())->toBe(OrisPaymentState::PartiallyPaid)
        ->and($event->orisStatus?->is($status))->toBeTrue();

    Http::assertSent(fn (Request $request): bool => $request['method'] === 'getEventBalance' && (int) $request['eventid'] === 109642);
});

it('stores no settlement when the club has no entries in ORIS', function (): void {
    fakeOrisEventStatus(null);
    $event = SportEvent::factory()->create(['oris_id' => 109642]);

    $status = (new OrisEventStatusRefresher())->refresh($event);

    expect($status->hasClubBalance())->toBeFalse()
        ->and($status->paymentState())->toBeNull()
        ->and($status->qrPayment())->toBeNull()
        ->and($status->bank_account)->toBe('2901296231/2010');
});

it('throws on an ORIS error but refreshQuietly keeps the stored status', function (): void {
    Http::fake(['*' => Http::response(['Method' => 'getEvent', 'Format' => 'json', 'Status' => 'ID not valid', 'ExportCreated' => now()->toDateTimeString(), 'Data' => []])]);
    $event = SportEvent::factory()->create(['oris_id' => 109642]);
    $stored = SportEventOrisStatus::factory()->create(['sport_event_id' => $event->id, 'fetched_at' => now()->subDay()]);
    $refresher = new OrisEventStatusRefresher();

    expect(fn () => $refresher->refresh($event))->toThrow(HttpClientException::class)
        ->and($refresher->refreshQuietly($event->fresh() ?? $event)?->is($stored))->toBeTrue()
        ->and($stored->fresh()?->fetched_at->lt(now()->subHours(23)))->toBeTrue();
});

it('refreshes only a missing or stale status', function (): void {
    fakeOrisEventStatus(orisClubBalanceRow(entry: 400, service: 0, paid: 0));
    $fresh = SportEvent::factory()->create(['oris_id' => 101001]);
    SportEventOrisStatus::factory()->create(['sport_event_id' => $fresh->id, 'fetched_at' => now()->subMinutes(10)]);
    $stale = SportEvent::factory()->create(['oris_id' => 101002]);
    SportEventOrisStatus::factory()->create(['sport_event_id' => $stale->id, 'fetched_at' => now()->subHours(2)]);
    $missing = SportEvent::factory()->create(['oris_id' => 101003]);

    $refresher = new OrisEventStatusRefresher();
    $refresher->refreshIfStale($fresh);
    $refresher->refreshIfStale($stale);
    $refresher->refreshIfStale($missing);

    $askedFor = Http::recorded()->map(fn (array $pair): int => (int) ($pair[0]['eventid'] ?? $pair[0]['id']))->unique()->values()->all();
    expect($askedFor)->toBe([101002, 101003])
        ->and($missing->fresh()?->orisStatus?->fee_total)->toBe(440.0);
});

it('never calls ORIS in demo mode', function (): void {
    config(['demo.enabled' => true]);
    Http::fake();
    $event = SportEvent::factory()->create(['oris_id' => 990001]);
    $seeded = SportEventOrisStatus::factory()->create(['sport_event_id' => $event->id, 'fetched_at' => now()->subDays(3)]);

    expect((new OrisEventStatusRefresher())->refreshIfStale($event)?->is($seeded))->toBeTrue();
    Http::assertNothingSent();
});

it('syncs only recent races with club entries that are not settled yet', function (): void {
    fakeOrisEventStatus(orisClubBalanceRow(entry: 300, service: 0, paid: 0));
    $upcoming = SportEvent::factory()->create(['oris_id' => 102001, 'date' => now()->addDays(5)]);
    orisStatusEntry($upcoming);
    $settled = SportEvent::factory()->create(['oris_id' => 102002, 'date' => now()->subDays(3)]);
    orisStatusEntry($settled);
    SportEventOrisStatus::factory()->paid()->published(results: true)->create(['sport_event_id' => $settled->id]);
    $cancelledOnly = SportEvent::factory()->create(['oris_id' => 102003, 'date' => now()->addDays(5)]);
    orisStatusEntry($cancelledOnly, EntryStatus::Cancel);
    $farAhead = SportEvent::factory()->create(['oris_id' => 102004, 'date' => now()->addDays(SyncOrisEventStatuses::DAYS_AHEAD + 10)]);
    orisStatusEntry($farAhead);

    (new SyncOrisEventStatuses())->run();

    expect($upcoming->fresh()?->orisStatus?->to_be_paid)->toBe(340.0);
    Http::assertNotSent(fn (Request $request): bool => in_array((int) ($request['eventid'] ?? $request['id']), [102002, 102003, 102004], true));
});

it('shows the payment pill with account and variable symbol to a billing specialist', function (): void {
    $this->actingAs(orisStatusUser(AppRoles::BillingSpecialist));
    $event = SportEvent::factory()->create(['oris_id' => 109642]);
    SportEventOrisStatus::factory()->create([
        'sport_event_id' => $event->id,
        'bank_account' => '2901296231/2010',
        'fee_entry' => 520, 'fee_si' => 0, 'fee_service' => 160, 'fee_total' => 680,
        'paid' => 520, 'to_be_paid' => 160,
        'payment_vs' => '2631104',
        'starts_count' => 0,
    ]);

    livewire(OrisEventHeader::class, ['sportEvent' => $event->fresh()])
        ->assertSee('Doplatit 160')
        ->assertSee('2901296231/2010')
        ->assertSee('2631104')
        ->assertSee('data:image/svg+xml;base64,', escape: false)
        ->assertSee('https://oris.ceskyorientak.cz/Startovka?id=109642', escape: false)
        ->assertSee(__('sport-event.oris_status.start_list_missing'));
});

it('hides the club money from a member but keeps the ORIS links', function (): void {
    $this->actingAs(orisStatusUser(AppRoles::Member));
    $event = SportEvent::factory()->create(['oris_id' => 109642]);
    SportEventOrisStatus::factory()->published(results: true)->create(['sport_event_id' => $event->id, 'paid' => 0]);

    livewire(OrisEventHeader::class, ['sportEvent' => $event->fresh()])
        ->assertDontSee(__('sport-event.oris_status.popover.heading'))
        ->assertDontSee('Nezaplaceno')
        ->assertSee('https://oris.ceskyorientak.cz/Vysledky?id=109642', escape: false)
        ->assertDontSee(__('sport-event.oris_status.results_missing'));
});

it('warns when a manual reload from ORIS fails', function (): void {
    Http::fake(['*' => Http::response(status: 500)]);
    $this->actingAs(orisStatusUser(AppRoles::BillingSpecialist));
    $event = SportEvent::factory()->create(['oris_id' => 109642]);
    SportEventOrisStatus::factory()->create(['sport_event_id' => $event->id]);

    livewire(OrisEventHeader::class, ['sportEvent' => $event->fresh()])
        ->call('refreshFromOris')
        ->assertNotified(__('sport-event.oris_status.notifications.refresh_failed'));
});
