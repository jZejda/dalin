<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;

function popoverCredit(): UserCredit
{
    return UserCredit::query()->create([
        'user_id' => User::factory()->create()->id,
        'amount' => 100,
        'currency' => 'CZK',
        'source' => UserCredit::SOURCE_USER,
        'status' => UserCreditStatus::Done,
        'credit_type' => UserCreditType::MembershipFees,
    ]);
}

it('renders non-internal notes grouped by author and hides internal ones', function (): void {
    $alice = User::factory()->create(['name' => 'Alice Nováková']);
    $bob = User::factory()->create(['name' => 'Bob Dvořák']);
    $credit = popoverCredit();

    foreach ([[$alice, 'První <b>x</b>', false], [$alice, 'Druhá', false], [$bob, 'Od Boba', false], [$bob, 'INTERNAL-ORIS', true]] as [$u, $text, $internal]) {
        UserCreditNote::create(['user_credit_id' => $credit->id, 'note_user_id' => $u->id, 'note' => $text, 'internal' => $internal]);
    }

    $html = view('components.credit-notes-popover', ['credit' => $credit->fresh()->load('userCreditNotes.userNoteMade')])->render();

    expect($html)
        ->toContain('Od Boba')
        ->not->toContain('INTERNAL-ORIS')
        ->not->toContain('<b>x</b>')
        ->and(substr_count($html, 'Alice Nováková'))->toBe(1)
        ->and(substr_count($html, 'Bob Dvořák'))->toBe(1);
});

it('renders nothing when there are no visible notes', function (): void {
    $credit = popoverCredit();

    expect(trim(view('components.credit-notes-popover', ['credit' => $credit->load('userCreditNotes')])->render()))->toBe('');
});
