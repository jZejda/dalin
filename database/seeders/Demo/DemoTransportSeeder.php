<?php

declare(strict_types=1);

namespace Database\Seeders\Demo;

use App\Enums\SportEventTransportType;
use App\Enums\TransportDirection;
use App\Enums\TransportRequestStatus;
use App\Enums\VehicleType;
use App\Models\SportEvent;
use App\Models\TransportOffer;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Demo data for the transport (carpool) module.
 *
 * The scenario is deterministic so every state of the UI can be showcased
 * (and screenshotted): pending/approved/rejected/cancelled requests, requests
 * with passenger notes, club vehicles, a fully booked offer (grey "0" badge in
 * the event list) and events of every transport type. The fixed demo member
 * (member@demo.cz) appears both as a driver and as a passenger.
 */
class DemoTransportSeeder extends Seeder
{
    public function run(): void
    {
        $memberDemo = User::where('email', 'member@demo.cz')->first();
        /** @var Collection<int, User> $others */
        $others = User::role('member')->where('email', '!=', 'member@demo.cz')->get();

        if ($memberDemo === null || $others->count() < 5) {
            return;
        }

        /** @var Collection<int, SportEvent> $events */
        $events = SportEvent::where('date', '>=', Carbon::today())
            ->orderBy('date')
            ->take(5)
            ->get()
            ->values();

        if ($events->count() < 5) {
            return;
        }

        [$driverA, $driverB, $driverC, $passengerA, $passengerB] = $others->take(5)->all();
        [$event0, $event1, $event2, $event3, $event4] = $events->all();

        // Transport type per upcoming event: the default (self only) hides club vehicles
        $types = [
            SportEventTransportType::Combined,
            SportEventTransportType::ClubOnly,
            SportEventTransportType::Combined,
            SportEventTransportType::SelfOnly,
            SportEventTransportType::SelfOnly,
        ];
        foreach ($events as $index => $event) {
            $event->update(['transport_type' => $types[$index]]);
        }

        // Own vehicles of members
        $memberCar = $this->vehicle($memberDemo, 'Rodinné kombi', 'Škoda', 5);
        $carA = $this->vehicle($driverA, 'Osobní auto', 'Volkswagen', 4);
        $carB = $this->vehicle($driverB, 'Karavan', 'Ford', 5);
        $carC = $this->vehicle($driverC, 'Služební auto', 'Hyundai', 5);

        // Club vehicles (no owner)
        $clubVan = Vehicle::factory()->ofType(VehicleType::Van)->create([
            'name'     => 'Klubová dodávka',
            'brand'    => 'Ford',
            'seats'    => 9,
            'operator' => 'SK Demo Orientace',
        ]);

        // Combined event: member demo drives, requests in every state
        $offerMember = $this->offer($event0, $memberDemo, $memberCar, 'Brno, Riviéra', 4, 210, 150.0);
        $this->request($offerMember, $passengerA, TransportRequestStatus::Approved, 1, 'Mám s sebou kolo na střeše.');
        $this->request($offerMember, $passengerB, TransportRequestStatus::Pending, 2, 'Nastoupím až u nádraží, děkuju.', TransportDirection::There);
        $this->request($offerMember, $driverC, TransportRequestStatus::Rejected, 1);
        $this->request($offerMember, $driverA, TransportRequestStatus::Cancelled, 1, 'Nakonec jedu vlastním autem.');

        // Combined event: another private offer next to the club one
        $offerA = $this->offer($event0, $driverA, $carA, 'Praha, Černý Most', 3, 120, null);
        $this->request($offerA, $driverB, TransportRequestStatus::Pending, 1, 'Díky, jedu s dítětem.');

        // Club only event: club van offered by an event master, member demo asks for a seat
        $clubDriver = User::role('event_master')->first() ?? $driverB;
        $offerClub = $this->offer($event1, $clubDriver, $clubVan, 'Parkoviště u klubovny', 8, null, null, TransportDirection::Both);
        $this->request($offerClub, $memberDemo, TransportRequestStatus::Approved, 2, 'Jedeme s dcerou.');
        $this->request($offerClub, $passengerA, TransportRequestStatus::Approved, 3);
        $this->request($offerClub, $passengerB, TransportRequestStatus::Pending, 1, 'Kolik zavazadel se vejde?');

        // Combined event: member demo waits for approval, offers differ in direction
        $offerC = $this->offer($event2, $driverC, $carC, 'Olomouc, autobusové nádraží', 4, 95, 100.0);
        $this->request($offerC, $memberDemo, TransportRequestStatus::Pending, 1, 'Můžu vystoupit i cestou v Prostějově?');
        $offerB = $this->offer($event2, $driverB, $carB, 'Ostrava, Poruba', 4, 60, 200.0, TransportDirection::Back);
        $this->request($offerB, $passengerA, TransportRequestStatus::Approved, 2, null, TransportDirection::Back);

        // Self-only event: an inactive offer stays visible only to its owner
        $inactive = $this->offer($event3, $driverA, $carA, 'Pardubice, Dukla', 3, 140, 100.0);
        $inactive->update(['active' => false]);

        // Self-only event: fully booked offer (grey "0" badge in the event list)
        $fullOffer = $this->offer($event4, $driverC, $carC, 'Hradec Králové, Nový Hradec', 2, 80, 100.0);
        $this->request($fullOffer, $passengerA, TransportRequestStatus::Approved, 1);
        $this->request($fullOffer, $passengerB, TransportRequestStatus::Approved, 1, 'Ráno budu čekat před hlavním vchodem.');
    }

    private function vehicle(User $owner, string $name, string $brand, int $seats): Vehicle
    {
        return Vehicle::factory()->ownedBy($owner)->default()->create([
            'name'  => $name,
            'brand' => $brand,
            'type'  => VehicleType::PassengerCar->value,
            'seats' => $seats,
        ]);
    }

    private function offer(
        SportEvent $event,
        User $driver,
        Vehicle $vehicle,
        string $departurePlace,
        int $seats,
        ?int $distanceKm,
        ?float $contribution,
        TransportDirection $direction = TransportDirection::Both,
    ): TransportOffer {
        return TransportOffer::factory()->create([
            'sport_event_id'  => $event->id,
            'user_id'         => $driver->id,
            'vehicle_id'      => $vehicle->id,
            'departure_place' => $departurePlace,
            'direction'       => $direction,
            'seats_offered'   => $seats,
            'distance_km'     => $distanceKm,
            'contribution'    => $contribution,
            'active'          => true,
        ]);
    }

    private function request(
        TransportOffer $offer,
        User $passenger,
        TransportRequestStatus $status,
        int $seats,
        ?string $note = null,
        TransportDirection $direction = TransportDirection::Both,
    ): TransportRequest {
        return TransportRequest::factory()->create([
            'transport_offer_id' => $offer->id,
            'user_id'            => $passenger->id,
            'direction'          => $direction,
            'seats'              => $seats,
            'note'               => $note,
            'status'             => $status,
            'approved_at'        => $status === TransportRequestStatus::Approved ? Carbon::now() : null,
        ]);
    }
}
