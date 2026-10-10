<?php

declare(strict_types=1);

use App\Enums\AppRoles;
use App\Enums\EntryStatus;
use App\Http\Controllers\Cron\Jobs\EntryEndsToPay;
use App\Mail\EntryEndsToPay as EntryEndsToPayMail;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
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

it('sends billing specialists the races whose deadline ends this hour and have active entries', function (): void {
    Mail::fake();
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
