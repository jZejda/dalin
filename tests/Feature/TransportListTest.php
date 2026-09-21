<?php

declare(strict_types=1);

use App\Enums\SportEventTransportType;
use App\Enums\TransportDirection;
use App\Enums\TransportRequestStatus;
use App\Livewire\SportEvent\MyTransportRequestsList;
use App\Livewire\SportEvent\RequestsForMyOffersList;
use App\Livewire\SportEvent\TransportList;
use App\Mail\TransportRequestCreated;
use App\Models\SportEvent;
use App\Models\SportList;
use App\Models\TransportOffer;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

function makeTransportListEvent(): SportEvent
{
    $sportList = SportList::query()->create(['short_name' => 'OB']);

    return SportEvent::factory()->create([
        'sport_id' => $sportList->id,
        'discipline_id' => null,
        'level_id' => null,
        'transport_type' => SportEventTransportType::SelfOnly,
        'use_oris_for_entries' => false,
        'date' => now()->addMonth(),
    ]);
}

function makeTransportListOffer(SportEvent $event, int $seats = 4, bool $active = true): TransportOffer
{
    $driver = User::factory()->create();
    $vehicle = Vehicle::factory()->ownedBy($driver)->create();

    return TransportOffer::factory()->create([
        'sport_event_id' => $event->id,
        'user_id' => $driver->id,
        'vehicle_id' => $vehicle->id,
        'direction' => TransportDirection::Both,
        'seats_offered' => $seats,
        'active' => $active,
    ]);
}

beforeEach(function (): void {
    Mail::fake();
});

it('persists the passenger note when requesting a seat', function (): void {
    $event = makeTransportListEvent();
    $offer = makeTransportListOffer($event);
    $passenger = User::factory()->create(['active' => true]);
    $this->actingAs($passenger);

    Livewire::test(TransportList::class, ['sportEvent' => $event])
        ->callTableAction('requestSeat', $offer, [
            'direction' => TransportDirection::Both->value,
            'seats' => 1,
            'note' => 'Vezmu si dvě tašky.',
        ])
        ->assertHasNoTableActionErrors();

    expect(TransportRequest::query()->where('user_id', $passenger->id)->value('note'))->toBe('Vezmu si dvě tašky.');

    Mail::assertQueued(
        TransportRequestCreated::class,
        fn (TransportRequestCreated $mail): bool => str_contains($mail->render(), 'Vezmu si dvě tašky.'),
    );
});

it('cancels own request via the confirmation table action', function (): void {
    $event = makeTransportListEvent();
    $offer = makeTransportListOffer($event);
    $passenger = User::factory()->create(['active' => true]);
    $this->actingAs($passenger);

    $request = TransportRequest::factory()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => $passenger->id,
    ]);

    Livewire::test(MyTransportRequestsList::class, ['sportEvent' => $event])
        ->assertTableActionVisible('cancelRequest', $request)
        ->callTableAction('cancelRequest', $request)
        ->assertDispatched('transport-requests-changed');

    expect($request->fresh()?->status)->toBe(TransportRequestStatus::Cancelled);
});

it('lists only own requests and hides cancel for finished ones', function (): void {
    $event = makeTransportListEvent();
    $offer = makeTransportListOffer($event);
    $passenger = User::factory()->create(['active' => true]);
    $this->actingAs($passenger);

    $mine = TransportRequest::factory()->rejected()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => $passenger->id,
    ]);
    $foreign = TransportRequest::factory()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => User::factory()->create()->id,
    ]);

    Livewire::test(MyTransportRequestsList::class, ['sportEvent' => $event])
        ->assertCanSeeTableRecords([$mine])
        ->assertCanNotSeeTableRecords([$foreign])
        ->assertTableActionHidden('cancelRequest', $mine);
});

it('renders nothing in the request tables when there are no requests', function (): void {
    $event = makeTransportListEvent();
    $this->actingAs(User::factory()->create(['active' => true]));

    Livewire::test(MyTransportRequestsList::class, ['sportEvent' => $event])
        ->assertDontSee(__('transport.my_requests'));
    Livewire::test(RequestsForMyOffersList::class, ['sportEvent' => $event])
        ->assertDontSee(__('transport.requests_for_my_offers'));
});

it('lets the driver approve and reject requests from the table', function (): void {
    $event = makeTransportListEvent();
    $offer = makeTransportListOffer($event);
    $toApprove = TransportRequest::factory()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => User::factory()->create()->id,
    ]);
    $toReject = TransportRequest::factory()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => User::factory()->create()->id,
    ]);

    $this->actingAs($offer->user);

    Livewire::test(RequestsForMyOffersList::class, ['sportEvent' => $event])
        ->callTableAction('approve', $toApprove)
        ->callTableAction('reject', $toReject)
        ->assertDispatched('transport-requests-changed');

    expect($toApprove->fresh()?->status)->toBe(TransportRequestStatus::Approved)
        ->and($toReject->fresh()?->status)->toBe(TransportRequestStatus::Rejected);
});

it('shows only requests for the drivers own offers', function (): void {
    $event = makeTransportListEvent();
    $ownOffer = makeTransportListOffer($event);
    $otherOffer = makeTransportListOffer($event);
    $own = TransportRequest::factory()->create([
        'transport_offer_id' => $ownOffer->id,
        'user_id' => User::factory()->create()->id,
    ]);
    $other = TransportRequest::factory()->create([
        'transport_offer_id' => $otherOffer->id,
        'user_id' => User::factory()->create()->id,
    ]);

    $this->actingAs($ownOffer->user);

    Livewire::test(RequestsForMyOffersList::class, ['sportEvent' => $event])
        ->assertCanSeeTableRecords([$own])
        ->assertCanNotSeeTableRecords([$other]);
});

it('sums free seats over active offers including the own one', function (): void {
    $event = makeTransportListEvent();
    $ownOffer = makeTransportListOffer($event, 3);
    makeTransportListOffer($event, 4);
    makeTransportListOffer($event, 5, active: false);

    TransportRequest::factory()->approved()->create([
        'transport_offer_id' => $ownOffer->id,
        'user_id' => User::factory()->create()->id,
        'seats' => 2,
    ]);

    expect(TransportOffer::totalFreeSeatsForEvent($event->id))->toBe(5);

    $this->actingAs($ownOffer->user);

    Livewire::test(TransportList::class, ['sportEvent' => $event])
        ->assertDispatched('transport-free-seats-changed', count: 5);
});

it('shows request notes and user identity in both request tables', function (): void {
    $event = makeTransportListEvent();
    $offer = makeTransportListOffer($event);
    $passenger = User::factory()->create(['active' => true, 'name' => 'Petr Spolujezdec']);
    TransportRequest::factory()->create([
        'transport_offer_id' => $offer->id,
        'user_id' => $passenger->id,
        'note' => 'Čekám u nádraží.',
    ]);

    $this->actingAs($offer->user);
    Livewire::test(RequestsForMyOffersList::class, ['sportEvent' => $event])
        ->assertSee('Čekám u nádraží.')
        ->assertSee('Petr Spolujezdec')
        ->assertSee($passenger->email);

    $this->actingAs($passenger);
    Livewire::test(MyTransportRequestsList::class, ['sportEvent' => $event])
        ->assertSee('Čekám u nádraží.')
        ->assertSee((string) $offer->user?->email);
});
