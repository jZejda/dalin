<?php

declare(strict_types=1);

use App\Enums\AppRoles;
use App\Enums\EntryStatus;
use App\Http\Controllers\Cron\Jobs\EntryEndsToPay;
use App\Mail\EntryEndsToPay as EntryEndsToPayMail;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\SportEventOrisStatus;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

function entryEndsToPayEntry(SportEvent $event, EntryStatus $status): UserEntry
{
    $definition = SportClassDefinition::query()->firstOrCreate(
        ['sport_id' => $event->sport_id, 'name' => 'H21'],
        ['age_from' => 21, 'age_to' => 34, 'gender' => 'M'],
    );
    $profile = UserRaceProfile::factory()->create();

    return UserEntry::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $definition->id,
        'user_race_profile_id' => $profile->id,
        'class_name' => 'H21',
        'entry_status' => $status->value,
        'rent_si' => false,
        'entry_created' => now(),
    ]);
}

function entryEndsToPayBilling(): User
{
    Role::firstOrCreate(['name' => AppRoles::BillingSpecialist->value, 'guard_name' => 'web']);
    $billing = User::factory()->create(['active' => true]);
    $billing->assignRole(AppRoles::BillingSpecialist->value);

    return $billing;
}

beforeEach(function (): void {
    config(['site-config.club.abbr' => 'ABM', 'demo.enabled' => false]);
    Http::preventStrayRequests();
});

it('sends billing specialists the races whose deadline ends this hour and have active entries', function (): void {
    Mail::fake();
    // Factories give events a random ORIS ID; ORIS being down keeps the mail without amounts
    Http::fake(['*' => Http::response(status: 503)]);
    $this->travelTo(now()->setTime(18, 20));
    Role::firstOrCreate(['name' => AppRoles::BillingSpecialist->value, 'guard_name' => 'web']);
    $billing = User::factory()->create(['active' => true]);
    $billing->assignRole(AppRoles::BillingSpecialist->value);

    $due = SportEvent::factory()->create(['name' => 'Středeční trénink Testov', 'entry_date_1' => now()->setTime(18, 59)]);
    entryEndsToPayEntry($due, EntryStatus::Create);
    $cancelledOnly = SportEvent::factory()->create(['name' => 'Zrušená účast Testov', 'entry_date_1' => now()->setTime(18, 59)]);
    entryEndsToPayEntry($cancelledOnly, EntryStatus::Cancel);

    (new EntryEndsToPay())->run();

    Mail::assertSent(EntryEndsToPayMail::class, function (EntryEndsToPayMail $mail) use ($billing): bool {
        if (! $mail->hasTo($billing->email)) {
            return false;
        }

        $mail->assertSeeInHtml('Středeční trénink Testov')
            ->assertDontSeeInHtml('Zrušená účast Testov');

        return true;
    });
});

it('adds the outstanding amount, organizer account and variable symbol freshly loaded from ORIS', function (): void {
    Mail::fake();
    Http::fake(function (Request $request) {
        $data = $request['method'] === 'getEvent'
            ? ['EntryBankAccount' => '2901296231/2010', 'CurrentStartsCount' => 0, 'CurrentResultsCount' => 0]
            : ['Currency' => 'CZK', 'Clubs' => ['Club_1' => [
                'ClubAbbr' => 'ABM', 'FeeEntry' => 520, 'FeeSI' => 0, 'FeeService' => 160,
                'FeeTotal' => 680, 'Paid' => 520, 'ToBePaid' => 160, 'PaymentVS' => '2631104',
            ]]];

        return Http::response(['Method' => $request['method'], 'Format' => 'json', 'Status' => 'OK', 'ExportCreated' => now()->toDateTimeString(), 'Data' => $data]);
    });
    $this->travelTo(now()->setTime(18, 20));
    $billing = entryEndsToPayBilling();

    $due = SportEvent::factory()->create(['name' => 'Harrachov Testov', 'oris_id' => 109642, 'entry_date_2' => now()->setTime(18, 59)]);
    entryEndsToPayEntry($due, EntryStatus::Create);
    // Stale amount from before the second wave — the mail must not use it
    SportEventOrisStatus::factory()->create(['sport_event_id' => $due->id, 'fee_total' => 520, 'paid' => 0, 'to_be_paid' => 520, 'fetched_at' => now()->subDay()]);

    (new EntryEndsToPay())->run();

    Mail::assertSent(EntryEndsToPayMail::class, function (EntryEndsToPayMail $mail) use ($billing): bool {
        $mail->assertSeeInText('K úhradě: 160')
            ->assertSeeInHtml('2901296231/2010')
            ->assertSeeInHtml('2631104')
            ->assertSeeInHtml('už uhrazeno 520')
            ->assertSeeInHtml('Částky jsou podle vyúčtování klubu v ORIS');

        return $mail->hasTo($billing->email);
    });
});

it('reports an already settled race as paid instead of asking for money', function (): void {
    $event = SportEvent::factory()->create(['name' => 'Zaplacený Testov', 'oris_id' => 109642, 'entry_date_1' => now()->addHour()]);
    SportEventOrisStatus::factory()->create(['sport_event_id' => $event->id, 'fee_total' => 600, 'paid' => 600, 'to_be_paid' => 0]);

    (new EntryEndsToPayMail(collect([$event->fresh()]), 1))
        ->assertSeeInHtml('Podle ORIS uhrazeno (600')
        ->assertDontSeeInText('K úhradě');
});
