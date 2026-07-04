<?php

declare(strict_types=1);

use App\Enums\ServiceOrderStatus;
use App\Enums\UserCreditType;
use App\Enums\UserParamType;
use App\Models\SportEvent;
use App\Models\SportService;
use App\Models\SportServiceOrder;
use App\Models\SportServicePaymentDate;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserParam;
use App\Models\UserRaceProfile;
use App\Services\SportEvents\Services\OrisServiceEntryClient;
use App\Services\SportEvents\Services\OrisServiceEntryResponse;
use App\Services\SportEvents\Services\ServiceOrderBiller;
use App\Services\SportEvents\Services\ServiceOrderCanceller;
use App\Services\SportEvents\Services\ServiceOrderCreator;

function makeServiceOrderUser(float $balance = 1000.0): User
{
    $user = User::factory()->create(['active' => true]);

    $param = new UserParam();
    $param->user_id = $user->id;
    $param->type = UserParamType::UserActualBalance;
    $param->value = $balance;
    $param->save();

    return $user;
}

function makeRaceProfileFor(User $user, ?string $clubUserId = null): UserRaceProfile
{
    return UserRaceProfile::query()->create([
        'user_id' => $user->id,
        'first_name' => 'Test',
        'last_name' => 'Runner',
        'reg_number' => 'TST'.random_int(1000, 9999),
        'gender' => 'M',
        'active' => true,
        'club_user_id' => $clubUserId,
    ]);
}

beforeEach(function (): void {
    $this->user = makeServiceOrderUser();
    $this->raceProfile = makeRaceProfileFor($this->user);

    $this->event = SportEvent::factory()->create([
        'oris_id' => null,
        'use_oris_for_entries' => false,
        'cancelled' => false,
        'discipline_id' => null,
        'stages' => null,
        'sport_id' => 1,
    ]);

    $this->service = SportService::factory()->create([
        'sport_event_id' => $this->event->id,
        'oris_service_id' => null,
        'unit_price' => 150.0,
        'qty_available' => 10,
        'qty_already_ordered' => 0,
        'qty_remaining' => 10,
    ]);

    $this->paymentDate = SportServicePaymentDate::factory()->create([
        'sport_service_id' => $this->service->id,
        'created_by_user_id' => $this->user->id,
    ]);
});

describe('ServiceOrderCreator (interní závod)', function (): void {
    it('creates an internal order and adjusts service capacity', function (): void {
        $result = (new ServiceOrderCreator())->create(
            service: $this->service,
            raceProfile: $this->raceProfile,
            paymentDate: $this->paymentDate,
            qty: 2,
            note: 'poznámka',
            sourceUser: $this->user,
        );

        expect($result->success)->toBeTrue()
            ->and($result->order)->toBeInstanceOf(SportServiceOrder::class)
            ->and($result->order->oris_service_entry_id)->toBeNull()
            ->and($result->order->status)->toBe(ServiceOrderStatus::Ordered)
            ->and($result->order->unit_price)->toBe(150.0)
            ->and($result->order->totalAmount())->toBe(300.0);

        $this->service->refresh();
        expect($this->service->qty_remaining)->toBe(8)
            ->and($this->service->qty_already_ordered)->toBe(2);
    });

    it('rejects a payment date belonging to another service', function (): void {
        $otherService = SportService::factory()->create([
            'sport_event_id' => $this->event->id,
            'oris_service_id' => null,
        ]);
        $foreignDate = SportServicePaymentDate::factory()->create([
            'sport_service_id' => $otherService->id,
            'created_by_user_id' => $this->user->id,
        ]);

        $result = (new ServiceOrderCreator())->create(
            service: $this->service,
            raceProfile: $this->raceProfile,
            paymentDate: $foreignDate,
            qty: 1,
            note: null,
            sourceUser: $this->user,
        );

        expect($result->success)->toBeFalse()
            ->and($result->error)->toContain('termín');
    });

    it('rejects an order over the remaining capacity', function (): void {
        $result = (new ServiceOrderCreator())->create(
            service: $this->service,
            raceProfile: $this->raceProfile,
            paymentDate: $this->paymentDate,
            qty: 11,
            note: null,
            sourceUser: $this->user,
        );

        expect($result->success)->toBeFalse()
            ->and($result->error)->toContain('kapacitu');
    });

    it('allows any qty when the capacity is unlimited', function (): void {
        $this->service->qty_available = 0;
        $this->service->qty_remaining = 0;
        $this->service->save();

        $result = (new ServiceOrderCreator())->create(
            service: $this->service,
            raceProfile: $this->raceProfile,
            paymentDate: $this->paymentDate,
            qty: 25,
            note: null,
            sourceUser: $this->user,
        );

        expect($result->success)->toBeTrue();
    });

    it('rejects an order after the booking deadline', function (): void {
        $this->service->last_booking_date_time = now()->subDay()->format('Y-m-d H:i:s');
        $this->service->save();

        $result = (new ServiceOrderCreator())->create(
            service: $this->service,
            raceProfile: $this->raceProfile,
            paymentDate: $this->paymentDate,
            qty: 1,
            note: null,
            sourceUser: $this->user,
        );

        expect($result->success)->toBeFalse()
            ->and($result->error)->toContain('vypršel');
    });

    it('rejects an order when the user balance is below the credit limit', function (): void {
        $poorUser = makeServiceOrderUser(-5000.0);
        $poorProfile = makeRaceProfileFor($poorUser);

        $result = (new ServiceOrderCreator())->create(
            service: $this->service,
            raceProfile: $poorProfile,
            paymentDate: $this->paymentDate,
            qty: 1,
            note: null,
            sourceUser: $poorUser,
        );

        expect($result->success)->toBeFalse()
            ->and($result->error)->toContain('konta');
    });
});

describe('ServiceOrderCreator (ORIS závod)', function (): void {
    beforeEach(function (): void {
        $this->event->oris_id = 5555;
        $this->event->save();

        $this->service->oris_service_id = 777;
        $this->service->save();
        $this->service->refresh();

        $this->orisProfile = makeRaceProfileFor($this->user, clubUserId: '4242');
    });

    it('stores the ORIS service entry id on success', function (): void {
        $orisClient = Mockery::mock(OrisServiceEntryClient::class);
        $orisClient->shouldReceive('createServiceEntry')
            ->once()
            ->andReturn(new OrisServiceEntryResponse(status: 'OK', serviceEntryId: 98765));

        $result = (new ServiceOrderCreator($orisClient))->create(
            service: $this->service,
            raceProfile: $this->orisProfile,
            paymentDate: $this->paymentDate,
            qty: 1,
            note: null,
            sourceUser: $this->user,
        );

        expect($result->success)->toBeTrue()
            ->and($result->order->oris_service_entry_id)->toBe(98765);
    });

    it('fails when ORIS rejects the entry and stores nothing', function (): void {
        $orisClient = Mockery::mock(OrisServiceEntryClient::class);
        $orisClient->shouldReceive('createServiceEntry')
            ->once()
            ->andReturn(new OrisServiceEntryResponse(status: 'ERR_SERVICE_FULL'));

        $result = (new ServiceOrderCreator($orisClient))->create(
            service: $this->service,
            raceProfile: $this->orisProfile,
            paymentDate: $this->paymentDate,
            qty: 1,
            note: null,
            sourceUser: $this->user,
        );

        expect($result->success)->toBeFalse()
            ->and($result->error)->toContain('ERR_SERVICE_FULL')
            ->and(SportServiceOrder::query()->count())->toBe(0);
    });

    it('fails when the race profile has no ORIS club user id', function (): void {
        $result = (new ServiceOrderCreator())->create(
            service: $this->service,
            raceProfile: $this->raceProfile,
            paymentDate: $this->paymentDate,
            qty: 1,
            note: null,
            sourceUser: $this->user,
        );

        expect($result->success)->toBeFalse()
            ->and($result->error)->toContain('ORIS');
    });
});

describe('ServiceOrderCanceller', function (): void {
    it('cancels an internal order and restores the capacity', function (): void {
        $creator = new ServiceOrderCreator();
        $order = $creator->create($this->service, $this->raceProfile, $this->paymentDate, 3, null, $this->user)->order;

        $result = (new ServiceOrderCanceller())->cancel($order);

        expect($result->success)->toBeTrue()
            ->and($order->refresh()->status)->toBe(ServiceOrderStatus::Cancelled);

        $this->service->refresh();
        expect($this->service->qty_remaining)->toBe(10)
            ->and($this->service->qty_already_ordered)->toBe(0);
    });

    it('refuses to cancel a billed order', function (): void {
        $order = (new ServiceOrderCreator())
            ->create($this->service, $this->raceProfile, $this->paymentDate, 1, null, $this->user)
            ->order;
        $order->status = ServiceOrderStatus::Billed;
        $order->save();

        $result = (new ServiceOrderCanceller())->cancel($order);

        expect($result->success)->toBeFalse();
    });

    it('cancels the ORIS entry before cancelling the order', function (): void {
        $order = (new ServiceOrderCreator())
            ->create($this->service, $this->raceProfile, $this->paymentDate, 1, null, $this->user)
            ->order;
        $order->oris_service_entry_id = 123456;
        $order->save();

        $orisClient = Mockery::mock(OrisServiceEntryClient::class);
        $orisClient->shouldReceive('deleteServiceEntry')
            ->once()
            ->with(123456)
            ->andReturn(new OrisServiceEntryResponse(status: 'OK'));

        $result = (new ServiceOrderCanceller($orisClient))->cancel($order->refresh());

        expect($result->success)->toBeTrue()
            ->and($order->refresh()->status)->toBe(ServiceOrderStatus::Cancelled);
    });
});

describe('ServiceOrderBiller', function (): void {
    it('bills ordered services into user credits and is idempotent', function (): void {
        $billingUser = makeServiceOrderUser();

        (new ServiceOrderCreator())->create($this->service, $this->raceProfile, $this->paymentDate, 2, null, $this->user);

        $biller = new ServiceOrderBiller();
        $billed = $biller->billEvent($this->event, $billingUser);

        expect($billed)->toBe(1);

        /** @var UserCredit $credit */
        $credit = UserCredit::query()
            ->where('sport_event_id', '=', $this->event->id)
            ->whereNotNull('sport_service_order_id')
            ->first();

        expect($credit)->not->toBeNull()
            ->and($credit->credit_type)->toBe(UserCreditType::ServiceFee)
            ->and((float) $credit->amount)->toBe(-300.0)
            ->and($credit->user_id)->toBe($this->user->id)
            ->and($credit->sport_service_id)->toBe($this->service->id);

        $order = SportServiceOrder::query()->firstOrFail();
        expect($order->status)->toBe(ServiceOrderStatus::Billed);

        // Second run must not create duplicate credits.
        expect($biller->billEvent($this->event, $billingUser))->toBe(0)
            ->and(UserCredit::query()->whereNotNull('sport_service_order_id')->count())->toBe(1);
    });

    it('skips cancelled orders', function (): void {
        $billingUser = makeServiceOrderUser();

        $order = (new ServiceOrderCreator())
            ->create($this->service, $this->raceProfile, $this->paymentDate, 1, null, $this->user)
            ->order;
        (new ServiceOrderCanceller())->cancel($order);

        expect((new ServiceOrderBiller())->billEvent($this->event, $billingUser))->toBe(0);
    });
});
