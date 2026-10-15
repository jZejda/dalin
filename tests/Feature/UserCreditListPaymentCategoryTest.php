<?php

declare(strict_types=1);

use App\Enums\PaymentCategory;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Filament\Resources\UserCredits\Pages\ListUserCredits;
use App\Livewire\UserCredit\UserCreditList;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use App\Models\UserCredit;
use Illuminate\Support\Facades\App;
use Livewire\Livewire;

function paymentCategoryCredit(User $user, UserCreditType $type, float $amount): UserCredit
{
    return UserCredit::query()->create([
        'user_id' => $user->id,
        'amount' => $amount,
        'currency' => 'CZK',
        'source' => UserCredit::SOURCE_USER,
        'status' => UserCreditStatus::Done,
        'credit_type' => $type,
    ]);
}

it('shows the payment type with the shared payment category component', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);
    paymentCategoryCredit($user, UserCreditType::UserDonation, 500);

    Livewire::test(UserCreditList::class)
        // sm-sized payment category badge from x-payment-category-badge
        ->assertSeeHtml('width:32px;height:32px;')
        ->assertSee(__('user-credit.payment_category_enum.deposit'));
});

it('renders the credit list in English', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);
    paymentCategoryCredit($user, UserCreditType::UserDonation, 500);
    App::setLocale('en');

    Livewire::test(UserCreditList::class)
        ->assertSee(['Payment type', 'Registration', 'Total'])
        ->assertDontSee(['Typ platby', 'Registrace závodníka']);
});

it('shows and filters the payment type in the admin credit overview', function (): void {
    actingAsSuperAdmin();
    $member = User::factory()->create();
    $deposit = paymentCategoryCredit($member, UserCreditType::UserDonation, 500);
    $membershipFee = paymentCategoryCredit($member, UserCreditType::MembershipFees, -300);

    Livewire::test(ListUserCredits::class)
        ->assertTableColumnExists('payment_category')
        ->filterTable('payment_category', [PaymentCategory::Deposit->value])
        ->assertCanSeeTableRecords([$deposit])
        ->assertCanNotSeeTableRecords([$membershipFee]);
});

it('notifies billing admins about a payment note in their own language', function (): void {
    $member = User::factory()->create(['name' => 'Jana Nováková']);
    $credit = paymentCategoryCredit($member, UserCreditType::MembershipFees, -300);
    $admin = actingAsSuperAdmin();
    $admin->forceFill(['locale' => 'en'])->save();
    $this->actingAs($member);

    Livewire::test(UserCreditList::class)
        ->callAction(TestAction::make('createNewNote')->table($credit), ['user_note' => 'Platbu jsem poslal převodem.'])
        ->assertHasNoActionErrors();

    $notification = $admin->notifications()->latest()->first();

    expect($notification?->data['title'])->toBe('Billing note')
        ->and($notification?->data['body'])->toBe('User: Jana Nováková | Billing ID: '.$credit->id.' sent a message. Please resolve the note.');
});

it('shows the credit owner as a compact badge in the admin credit overview', function (): void {
    actingAsSuperAdmin();
    $member = User::factory()->create(['name' => 'Jana Nováková']);
    paymentCategoryCredit($member, UserCreditType::UserDonation, 500);

    Livewire::test(ListUserCredits::class)
        // Name only as the badge tooltip, no e-mail line from the full x-user-identity
        ->assertSeeHtml('Jana Nováková')
        ->assertDontSee($member->email);
});

it('shows the amount like the member finance balance', function (): void {
    actingAsSuperAdmin();
    $member = User::factory()->create();
    paymentCategoryCredit($member, UserCreditType::MembershipFees, -1250);

    Livewire::test(ListUserCredits::class)
        // Czech format: non-breaking space as thousands separator
        ->assertSeeHtml('<span class="text-danger-600">-1'."\u{00A0}".'250,00</span><span class="text-gray-400 dark:text-gray-500 text-sm ml-1">Kč</span>');
});
