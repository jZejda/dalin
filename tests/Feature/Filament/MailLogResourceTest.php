<?php

declare(strict_types=1);

use App\Filament\Clusters\Config\Resources\MailLogs\Pages\ListMailLogs;
use App\Models\MailLog;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function (): void {
    actingAsSuperAdmin();
});

// A future send date keeps these rows on the first page above any demo logs in the testing DB
function recipientLog(string $recipient, string $sentAt = '2031-03-04 17:05:13'): MailLog
{
    return MailLog::factory()->create(['recipient' => $recipient, 'created_at' => $sentAt]);
}

it('shows the send time under the date', function (): void {
    recipientLog('hotrusinova@example.cz');

    $this->get('/admin/config/mail-logs')
        ->assertOk()
        ->assertSee('04.03.2031')
        ->assertSee('17:05');
});

it('shows the recipient as a user identity when the address belongs to an account', function (): void {
    $user = User::factory()->create(['name' => 'Hana Otrusinová', 'email' => 'hotrusinova@example.cz']);
    recipientLog($user->email);

    $this->get('/admin/config/mail-logs')
        ->assertOk()
        ->assertSee('Hana Otrusinová')
        ->assertSee($user->initials)
        ->assertSee('hotrusinova@example.cz');
});

it('falls back to the plain address when the recipient has no account', function (): void {
    recipientLog('prihlasky@oris.example.cz');

    $this->get('/admin/config/mail-logs')
        ->assertOk()
        ->assertSee('prihlasky@oris.example.cz');
});

it('finds a log by the recipient name as well as the address', function (): void {
    $user = User::factory()->create(['name' => 'Hana Otrusinová', 'email' => 'hotrusinova@example.cz']);
    $matching = recipientLog($user->email);
    $other = recipientLog('marek.palat@example.cz');

    Livewire::test(ListMailLogs::class)
        ->searchTable('Otrusinová')
        ->assertCanSeeTableRecords([$matching])
        ->assertCanNotSeeTableRecords([$other])
        ->searchTable('marek.palat')
        ->assertCanSeeTableRecords([$other])
        ->assertCanNotSeeTableRecords([$matching]);
});
