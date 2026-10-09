<?php

declare(strict_types=1);

use App\Enums\AppRoles;
use App\Enums\EntryStatus;
use App\Enums\PaymentCategory;
use App\Enums\ServiceOrderStatus;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Filament\Clusters\Config\Resources\Users\RelationManagers\UserCreditRelationManager;
use App\Filament\Resources\MemberFinances\Pages\ViewMemberFinance;
use App\Livewire\SportEvent\RaceProfilePaymentList;
use App\Models\AppSetting;
use App\Models\SportClassDefinition;
use App\Models\SportEvent;
use App\Models\SportService;
use App\Models\SportServiceOrder;
use App\Models\SportServicePaymentDate;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use App\Services\SportEvents\Services\ServiceCreditManager;
use App\Services\SportEvents\Services\ServiceOrderBiller;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

function makeServiceCreditProfile(SportEvent $event): UserRaceProfile
{
    $user = User::factory()->create(['active' => true]);

    $profile = UserRaceProfile::query()->create([
        'user_id' => $user->id,
        'first_name' => 'Test',
        'last_name' => 'Runner',
        'reg_number' => 'TST'.random_int(1000, 9999),
        'gender' => 'M',
        'active' => true,
    ]);

    $classDefinition = SportClassDefinition::query()->firstOrCreate(
        ['sport_id' => 1, 'name' => 'H21'],
        ['age_from' => 18, 'age_to' => 40, 'gender' => 'M'],
    );

    UserEntry::query()->create([
        'sport_event_id' => $event->id,
        'class_definition_id' => $classDefinition->id,
        'user_race_profile_id' => $profile->id,
        'class_name' => 'H21',
        'entry_status' => EntryStatus::Create->value,
        'rent_si' => false,
        'entry_created' => now(),
    ]);

    return $profile;
}

function serviceCreditsOf(UserRaceProfile $profile): \Illuminate\Support\Collection
{
    return UserCredit::query()
        ->where('user_race_profile_id', $profile->id)
        ->orderBy('id')
        ->pluck('amount')
        ->map(fn ($amount): float => (float) $amount);
}

beforeEach(function (): void {
    AppSetting::set(AppSetting::SERVICE_ORDERS_MODULE_ENABLED, true);

    $this->event = SportEvent::factory()->create([
        'oris_id' => null,
        'use_oris_for_entries' => false,
        'discipline_id' => null,
        'stages' => null,
        'sport_id' => 1,
    ]);

    $this->service = SportService::factory()->create([
        'sport_event_id' => $this->event->id,
        'oris_service_id' => null,
        'service_name_cz' => 'Ubytování v tělocvičně',
        'unit_price' => 150.0,
    ]);

    $this->otherService = SportService::factory()->create([
        'sport_event_id' => $this->event->id,
        'oris_service_id' => null,
        'service_name_cz' => 'Parkování',
        'unit_price' => 50.0,
    ]);

    $this->billing = User::factory()->create(['active' => true]);
    $this->billing->assignRole(AppRoles::BillingSpecialist->value);

    $this->profile = makeServiceCreditProfile($this->event);
    $this->secondProfile = makeServiceCreditProfile($this->event);
});

describe('ServiceCreditManager', function (): void {
    it('charges qty × unit price as a service fee', function (): void {
        $created = (new ServiceCreditManager())->assign($this->service, [$this->profile], 2, 'Dvě noci', $this->billing);

        $credit = UserCredit::query()->where('user_race_profile_id', $this->profile->id)->sole();

        expect($created)->toBe(1)
            ->and((float) $credit->amount)->toBe(-300.0)
            ->and($credit->credit_type)->toBe(UserCreditType::ServiceFee)
            ->and($credit->sport_service_id)->toBe($this->service->id)
            ->and($credit->sport_event_id)->toBe($this->event->id)
            ->and($credit->user_id)->toBe($this->profile->user_id)
            ->and($credit->source_user_id)->toBe($this->billing->id)
            ->and($credit->userCreditNotes()->where('internal', true)->count())->toBe(1)
            ->and($credit->userCreditNotes()->where('internal', false)->value('note'))->toBe('Dvě noci');
    });

    it('reverses the outstanding charge with the opposite amount and keeps the original', function (): void {
        $manager = new ServiceCreditManager();
        $manager->assign($this->service, [$this->profile], 2, null, $this->billing);

        $reversed = $manager->reverse($this->service, [$this->profile], 2, null, $this->billing);

        expect($reversed)->toBe(1)
            ->and(serviceCreditsOf($this->profile)->all())->toBe([-300.0, 300.0])
            ->and($manager->chargedAmount($this->service, $this->profile))->toBe(0.0);
    });

    it('skips profiles without the service and does not reverse twice', function (): void {
        $manager = new ServiceCreditManager();
        $manager->assign($this->service, [$this->profile], 1, null, $this->billing);

        expect($manager->reverse($this->service, [$this->profile, $this->secondProfile], 1, null, $this->billing))->toBe(1)
            ->and($manager->reverse($this->service, [$this->profile], 1, null, $this->billing))->toBe(0)
            ->and(serviceCreditsOf($this->secondProfile)->all())->toBe([]);
    });

    it('lists only services with an outstanding charge for the given profiles', function (): void {
        $manager = new ServiceCreditManager();
        $manager->assign($this->service, [$this->profile], 1, null, $this->billing);
        $manager->assign($this->otherService, [$this->secondProfile], 1, null, $this->billing);
        $manager->reverse($this->otherService, [$this->secondProfile], 1, null, $this->billing);

        expect($manager->chargedServiceIds($this->event->id, [$this->profile->id, $this->secondProfile->id]))
            ->toBe([$this->service->id])
            ->and($manager->chargedServiceIds($this->event->id, [$this->secondProfile->id]))->toBe([]);
    });

    it('reverses only the requested quantity and caps it at the charged amount', function (): void {
        $manager = new ServiceCreditManager();
        $manager->assign($this->service, [$this->profile], 2, null, $this->billing);

        $manager->reverse($this->service, [$this->profile], 1, null, $this->billing);
        expect($manager->chargedAmount($this->service, $this->profile))->toBe(150.0);

        $manager->reverse($this->service, [$this->profile], 5, null, $this->billing);
        expect(serviceCreditsOf($this->profile)->all())->toBe([-300.0, 150.0, 150.0])
            ->and($manager->chargedAmount($this->service, $this->profile))->toBe(0.0);
    });

    it('cancels billed orders once the service is fully reversed', function (): void {
        $this->service->update(['qty_remaining' => 8, 'qty_already_ordered' => 2]);
        $paymentDate = SportServicePaymentDate::factory()->create([
            'sport_service_id' => $this->service->id,
            'created_by_user_id' => $this->billing->id,
        ]);
        $order = SportServiceOrder::factory()->create([
            'sport_event_id' => $this->event->id,
            'sport_service_id' => $this->service->id,
            'sport_service_payment_date_id' => $paymentDate->id,
            'user_id' => $this->profile->user_id,
            'user_race_profile_id' => $this->profile->id,
            'source_user_id' => $this->profile->user_id,
            'qty' => 2,
            'unit_price' => 150.0,
            'status' => ServiceOrderStatus::Ordered,
        ]);
        (new ServiceOrderBiller())->billEvent($this->event, $this->billing);

        $manager = new ServiceCreditManager();

        $manager->reverse($this->service, [$this->profile], 1, null, $this->billing);
        expect($order->refresh()->status)->toBe(ServiceOrderStatus::Billed);

        $manager->reverse($this->service, [$this->profile], 1, null, $this->billing);
        expect($order->refresh()->status)->toBe(ServiceOrderStatus::Cancelled)
            ->and($this->service->refresh()->qty_remaining)->toBe(10)
            ->and($this->service->qty_already_ordered)->toBe(0);
    });
});

describe('RaceProfilePaymentList service actions', function (): void {
    it('adds a service to the selected profiles', function (): void {
        $this->actingAs($this->billing);

        Livewire::test(RaceProfilePaymentList::class, ['sportEvent' => $this->event])
            ->selectTableRecords([$this->profile->id, $this->secondProfile->id])
            ->callAction(TestAction::make('assignEventService')->table()->bulk(), [
                'sport_service_id' => $this->service->id,
                'qty' => 1,
            ])
            ->assertHasNoFormErrors()
            ->assertNotified();

        expect(serviceCreditsOf($this->profile)->all())->toBe([-150.0])
            ->and(serviceCreditsOf($this->secondProfile)->all())->toBe([-150.0]);
    });

    it('reverses a service only for profiles that have it', function (): void {
        (new ServiceCreditManager())->assign($this->service, [$this->profile], 1, null, $this->billing);
        $this->actingAs($this->billing);

        Livewire::test(RaceProfilePaymentList::class, ['sportEvent' => $this->event])
            ->selectTableRecords([$this->profile->id, $this->secondProfile->id])
            ->callAction(TestAction::make('reverseEventService')->table()->bulk(), [
                'sport_service_id' => $this->service->id,
                'qty' => 1,
            ])
            ->assertHasNoFormErrors();

        expect(serviceCreditsOf($this->profile)->all())->toBe([-150.0, 150.0])
            ->and(serviceCreditsOf($this->secondProfile)->all())->toBe([]);
    });

    it('rejects reversing a service no selected profile has', function (): void {
        $this->actingAs($this->billing);

        Livewire::test(RaceProfilePaymentList::class, ['sportEvent' => $this->event])
            ->selectTableRecords([$this->profile->id])
            ->callAction(TestAction::make('reverseEventService')->table()->bulk(), [
                'sport_service_id' => $this->service->id,
            ])
            ->assertHasFormErrors(['sport_service_id']);

        expect(serviceCreditsOf($this->profile)->all())->toBe([]);
    });

    it('shows the service actions to billing roles only', function (?AppRoles $role, bool $visible): void {
        $user = User::factory()->create(['active' => true]);
        if ($role !== null) {
            $user->assignRole($role->value);
        }
        $this->actingAs($user);

        $component = Livewire::test(RaceProfilePaymentList::class, ['sportEvent' => $this->event]);

        foreach (['assignEventService', 'reverseEventService'] as $action) {
            $visible
                ? $component->assertActionVisible(TestAction::make($action)->table()->bulk())
                : $component->assertActionHidden(TestAction::make($action)->table()->bulk());
        }
    })->with([
        'billing specialist' => [AppRoles::BillingSpecialist, true],
        'super admin' => [AppRoles::SuperAdmin, true],
        'event master' => [AppRoles::EventMaster, false],
        'member' => [AppRoles::Member, false],
        'no role' => [null, false],
    ]);

    it('hides the service actions when the additional services module is off', function (): void {
        AppSetting::set(AppSetting::SERVICE_ORDERS_MODULE_ENABLED, false);
        $this->actingAs($this->billing);

        Livewire::test(RaceProfilePaymentList::class, ['sportEvent' => $this->event])
            ->assertActionHidden(TestAction::make('assignEventService')->table()->bulk())
            ->assertActionHidden(TestAction::make('reverseEventService')->table()->bulk())
            ->assertActionVisible(TestAction::make('assignEventPayment')->table()->bulk());
    });

    it('assigns the entry fee without a service link', function (): void {
        $this->actingAs($this->billing);

        Livewire::test(RaceProfilePaymentList::class, ['sportEvent' => $this->event])
            ->selectTableRecords([$this->profile->id])
            ->callAction(TestAction::make('assignEventPayment')->table()->bulk(), ['amount' => 250])
            ->assertHasNoFormErrors();

        $credit = UserCredit::query()->where('user_race_profile_id', $this->profile->id)->sole();

        expect((float) $credit->amount)->toBe(-250.0)
            ->and($credit->sport_service_id)->toBeNull()
            ->and(PaymentCategory::fromCredit($credit))->toBe(PaymentCategory::EntryFee);
    });
});

describe('PaymentCategory', function (): void {
    it('derives the category from the credit', function (array $attributes, PaymentCategory $expected): void {
        $credit = new UserCredit();
        $credit->forceFill($attributes);

        expect(PaymentCategory::fromCredit($credit))->toBe($expected);
    })->with([
        'entry fee' => [['credit_type' => UserCreditType::CashOut, 'sport_event_id' => 1], PaymentCategory::EntryFee],
        'manual deduction' => [['credit_type' => UserCreditType::CashOut, 'sport_event_id' => null], PaymentCategory::Other],
        'service fee' => [['credit_type' => UserCreditType::ServiceFee, 'sport_event_id' => 1], PaymentCategory::AdditionalService],
        'legacy service payment' => [['credit_type' => UserCreditType::CashOut, 'sport_event_id' => 1, 'sport_service_id' => 5], PaymentCategory::AdditionalService],
        'transport' => [['credit_type' => UserCreditType::TransportBilling], PaymentCategory::Transport],
        'marketplace' => [['credit_type' => UserCreditType::MarketplaceBilling], PaymentCategory::Marketplace],
        'membership' => [['credit_type' => UserCreditType::MembershipFees], PaymentCategory::MembershipFee],
        'deposit' => [['credit_type' => UserCreditType::UserDonation], PaymentCategory::Deposit],
        'initial deposit' => [['credit_type' => UserCreditType::InitialDeposit], PaymentCategory::InitialDeposit],
        'transfer' => [['credit_type' => UserCreditType::TransferCreditBetweenUsers], PaymentCategory::Transfer],
    ]);

    it('shows the payment type and service name in the member finance credit table', function (): void {
        $manager = new ServiceCreditManager();
        $manager->assign($this->service, [$this->profile], 1, null, $this->billing);
        $manager->reverse($this->service, [$this->profile], 1, null, $this->billing);

        $admin = User::factory()->create(['active' => true]);
        $admin->assignRole(AppRoles::SuperAdmin->value);
        $this->actingAs($admin);

        $owner = User::query()->findOrFail($this->profile->user_id);

        Livewire::test(UserCreditRelationManager::class, [
            'ownerRecord' => $owner,
            'pageClass' => ViewMemberFinance::class,
        ])
            ->assertCanSeeTableRecords(UserCredit::query()->where('user_id', $owner->id)->get())
            ->assertSee(PaymentCategory::AdditionalService->getLabel())
            ->assertSee('Ubytování v tělocvičně')
            ->assertSee(__('user-credit.table.payment_category_reversal'));
    });

    it('filters the credit table by payment type consistently with fromCredit()', function (): void {
        $owner = User::query()->findOrFail($this->profile->user_id);
        $samples = [
            [UserCreditType::CashOut, $this->event->id, null],
            [UserCreditType::CashOut, null, null],
            [UserCreditType::ServiceFee, $this->event->id, $this->service->id],
            [UserCreditType::CashOut, $this->event->id, $this->service->id],
            [UserCreditType::TransportBilling, $this->event->id, null],
            [UserCreditType::MarketplaceBilling, null, null],
            [UserCreditType::MembershipFees, null, null],
            [UserCreditType::UserDonation, null, null],
            [UserCreditType::InitialDeposit, null, null],
            [UserCreditType::TransferCreditBetweenUsers, null, null],
        ];

        foreach ($samples as [$type, $eventId, $serviceId]) {
            $credit = new UserCredit();
            $credit->user_id = $owner->id;
            $credit->sport_event_id = $eventId;
            $credit->sport_service_id = $serviceId;
            $credit->amount = -10;
            $credit->currency = UserCredit::CURRENCY_CZK;
            $credit->credit_type = $type;
            $credit->source = UserCredit::SOURCE_USER;
            $credit->status = UserCreditStatus::Done;
            $credit->save();
        }

        $credits = UserCredit::query()->where('user_id', $owner->id)->get();

        foreach (PaymentCategory::cases() as $category) {
            $query = UserCredit::query()->where('user_id', $owner->id);
            $category->constrain($query);

            expect($query->pluck('id')->sort()->values()->all())->toBe(
                $credits->filter(fn (UserCredit $c): bool => PaymentCategory::fromCredit($c) === $category)
                    ->pluck('id')->sort()->values()->all(),
                $category->value,
            );
        }

        $admin = User::factory()->create(['active' => true]);
        $admin->assignRole(AppRoles::SuperAdmin->value);
        $this->actingAs($admin);

        $entryFees = $credits->filter(fn (UserCredit $c): bool => PaymentCategory::fromCredit($c) === PaymentCategory::EntryFee);

        Livewire::test(UserCreditRelationManager::class, [
            'ownerRecord' => $owner,
            'pageClass' => ViewMemberFinance::class,
        ])
            ->filterTable('payment_category', [PaymentCategory::EntryFee->value])
            ->assertCanSeeTableRecords($entryFees)
            ->assertCanNotSeeTableRecords($credits->diff($entryFees))
            ->assertDontSeeHtml('heroicon-m-arrow-trending');
    });
});

describe('SportEvent::sport_event_oris_title', function (): void {
    it('joins only the present parts', function (?string $altName, ?int $orisId, string $expected): void {
        $event = new SportEvent();
        $event->forceFill(['name' => 'Oddílový přebor', 'alt_name' => $altName, 'oris_id' => $orisId]);

        expect($event->sport_event_oris_title)->toBe($expected);
    })->with([
        'name only' => [null, null, 'Oddílový přebor'],
        'with oris id' => [null, 9123, 'Oddílový přebor | (ORIS ID: 9123)'],
        'with alt name' => ['Přebor', 9123, 'Přebor | Oddílový přebor | (ORIS ID: 9123)'],
    ]);
});
