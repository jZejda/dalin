<?php

declare(strict_types=1);

use App\Enums\TransportDirection;
use App\Enums\TransportRequestStatus;
use App\Mail\TransportOfferCancelled;
use App\Mail\TransportRequestCancelled;
use App\Mail\TransportRequestCreated;
use App\Mail\TransportRequestDecided;
use App\Models\SportEvent;
use App\Models\SportList;
use App\Models\TransportOffer;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\TransportRequestService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

function makeServiceTestOffer(int $seats = 4): TransportOffer
{
    $sportList = SportList::query()->create(['short_name' => 'OB']);
    $sportEvent = SportEvent::factory()->create([
        'sport_id' => $sportList->id,
        'discipline_id' => null,
        'use_oris_for_entries' => false,
        'date' => now()->addMonth(),
    ]);
    $driver = User::factory()->create();
    $vehicle = Vehicle::factory()->ownedBy($driver)->create();

    return TransportOffer::factory()->create([
        'sport_event_id' => $sportEvent->id,
        'user_id' => $driver->id,
        'vehicle_id' => $vehicle->id,
        'direction' => TransportDirection::Both,
        'seats_offered' => $seats,
    ]);
}

beforeEach(function (): void {
    Mail::fake();
    $this->service = new TransportRequestService();
});

it('creates pending request and mails the driver', function (): void {
    $offer = makeServiceTestOffer();
    $requester = User::factory()->create();

    $request = $this->service->create($offer, $requester, TransportDirection::Both, 2);

    expect($request->isPending())->toBeTrue()
        ->and($request->seats)->toBe(2);

    Mail::assertQueued(
        TransportRequestCreated::class,
        fn (TransportRequestCreated $mail): bool => $mail->hasTo((string) $offer->user?->email),
    );
});

it('approves pending request and mails the requester', function (): void {
    $offer = makeServiceTestOffer();
    $requester = User::factory()->create();
    $request = $this->service->create($offer, $requester, TransportDirection::Both, 2);

    $result = $this->service->approve($request);

    $request->refresh();
    expect($result)->toBeTrue()
        ->and($request->isApproved())->toBeTrue()
        ->and($request->approved_at)->not->toBeNull();

    Mail::assertQueued(
        TransportRequestDecided::class,
        fn (TransportRequestDecided $mail): bool => $mail->hasTo($requester->email),
    );
});

it('rejects approval when capacity is exceeded', function (): void {
    $offer = makeServiceTestOffer(seats: 2);
    $requesterA = User::factory()->create();
    $requesterB = User::factory()->create();

    $requestA = $this->service->create($offer, $requesterA, TransportDirection::Both, 2);
    $requestB = $this->service->create($offer, $requesterB, TransportDirection::Both, 1);

    expect($this->service->approve($requestA))->toBeTrue()
        ->and($this->service->approve($requestB))->toBeFalse()
        ->and($requestB->refresh()->status)->toBe(TransportRequestStatus::Rejected);
});

it('approves requests in opposite directions independently', function (): void {
    $offer = makeServiceTestOffer(seats: 2);
    $requesterA = User::factory()->create();
    $requesterB = User::factory()->create();

    $requestThere = $this->service->create($offer, $requesterA, TransportDirection::There, 2);
    $requestBack = $this->service->create($offer, $requesterB, TransportDirection::Back, 2);

    expect($this->service->approve($requestThere))->toBeTrue()
        ->and($this->service->approve($requestBack))->toBeTrue();
});

it('is idempotent for already approved request', function (): void {
    $offer = makeServiceTestOffer();
    $requester = User::factory()->create();
    $request = $this->service->create($offer, $requester, TransportDirection::Both, 1);

    $this->service->approve($request);
    $approvedAt = $request->refresh()->approved_at;

    expect($this->service->approve($request))->toBeTrue()
        ->and($request->refresh()->approved_at?->equalTo($approvedAt))->toBeTrue();
});

it('rejects pending request and mails the requester', function (): void {
    $offer = makeServiceTestOffer();
    $requester = User::factory()->create();
    $request = $this->service->create($offer, $requester, TransportDirection::Both, 1);

    expect($this->service->reject($request))->toBeTrue()
        ->and($request->refresh()->status)->toBe(TransportRequestStatus::Rejected);

    Mail::assertQueued(TransportRequestDecided::class);
});

it('cancels approved request, frees seats and mails the driver', function (): void {
    $offer = makeServiceTestOffer(seats: 2);
    $requester = User::factory()->create();
    $request = $this->service->create($offer, $requester, TransportDirection::Both, 2);
    $this->service->approve($request);

    expect($offer->refresh()->load('requests')->freeSeats())->toBe(0);

    $this->service->cancel($request->refresh());

    expect($request->refresh()->status)->toBe(TransportRequestStatus::Cancelled)
        ->and($offer->refresh()->load('requests')->freeSeats())->toBe(2);

    Mail::assertQueued(
        TransportRequestCancelled::class,
        fn (TransportRequestCancelled $mail): bool => $mail->hasTo((string) $offer->user?->email),
    );
});

it('does not mail the driver when cancelling a pending request', function (): void {
    $offer = makeServiceTestOffer();
    $requester = User::factory()->create();
    $request = $this->service->create($offer, $requester, TransportDirection::Both, 1);

    $this->service->cancel($request);

    expect($request->refresh()->status)->toBe(TransportRequestStatus::Cancelled);
    Mail::assertNotQueued(TransportRequestCancelled::class);
});

it('cancels open requests and notifies requesters when offer is cancelled', function (): void {
    $offer = makeServiceTestOffer();
    $requesterA = User::factory()->create();
    $requesterB = User::factory()->create();

    $pending = $this->service->create($offer, $requesterA, TransportDirection::Both, 1);
    $approved = $this->service->create($offer, $requesterB, TransportDirection::Both, 1);
    $this->service->approve($approved);

    $this->service->cancelOffer($offer);

    expect($pending->refresh()->status)->toBe(TransportRequestStatus::Cancelled)
        ->and($approved->refresh()->status)->toBe(TransportRequestStatus::Cancelled);

    Mail::assertQueued(TransportOfferCancelled::class, 2);
});

it('approves request via signed link from email', function (): void {
    $offer = makeServiceTestOffer();
    $requester = User::factory()->create();
    $request = $this->service->create($offer, $requester, TransportDirection::Both, 1);

    $url = URL::temporarySignedRoute('transport-request.decision', now()->addDay(), [
        'transportRequest' => $request->id,
        'decision' => 'approve',
    ]);

    $this->get($url)->assertOk()->assertSee('schválena');

    expect($request->refresh()->isApproved())->toBeTrue();
});

it('rejects request via signed link from email', function (): void {
    $offer = makeServiceTestOffer();
    $requester = User::factory()->create();
    $request = $this->service->create($offer, $requester, TransportDirection::Both, 1);

    $url = URL::temporarySignedRoute('transport-request.decision', now()->addDay(), [
        'transportRequest' => $request->id,
        'decision' => 'reject',
    ]);

    $this->get($url)->assertOk()->assertSee('zamítnuta');

    expect($request->refresh()->status)->toBe(TransportRequestStatus::Rejected);
});

it('reports already decided request on repeated link click', function (): void {
    $offer = makeServiceTestOffer();
    $requester = User::factory()->create();
    $request = $this->service->create($offer, $requester, TransportDirection::Both, 1);
    $this->service->approve($request);

    $url = URL::temporarySignedRoute('transport-request.decision', now()->addDay(), [
        'transportRequest' => $request->id,
        'decision' => 'reject',
    ]);

    $this->get($url)->assertOk()->assertSee('vyřízená');

    expect($request->refresh()->isApproved())->toBeTrue();
});

it('denies decision link without valid signature', function (): void {
    $offer = makeServiceTestOffer();
    $requester = User::factory()->create();
    $request = $this->service->create($offer, $requester, TransportDirection::Both, 1);

    $this->get('/doprava/zadost/'.$request->id.'/approve')->assertForbidden();

    expect($request->refresh()->isPending())->toBeTrue();
});
