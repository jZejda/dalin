<?php

declare(strict_types=1);

namespace App\Mail\Concerns;

use App\Filament\Resources\SportEvents\SportEventResource;
use App\Models\SportEvent;
use App\Models\TransportOffer;
use App\Models\TransportRequest;

/**
 * Shared building blocks of the car-sharing e-mails in the club layout: the offer,
 * its race, the fact rows and the link to the race's Transport tab.
 */
trait DescribesTransportRequest
{
    /**
     * The offer is soft-deleted before a queued "offer cancelled" mail renders,
     * so it has to be resolved including trashed rows.
     */
    protected function transportOffer(TransportRequest $transportRequest): ?TransportOffer
    {
        if ($transportRequest->relationLoaded('transportOffer') && $transportRequest->transportOffer !== null) {
            return $transportRequest->transportOffer;
        }

        $offer = $transportRequest->transportOffer()->withTrashed()->with(['sportEvent', 'user', 'vehicle'])->first();
        $transportRequest->setRelation('transportOffer', $offer);

        return $offer;
    }

    protected function transportEvent(TransportRequest $transportRequest): ?SportEvent
    {
        return $this->transportOffer($transportRequest)?->sportEvent;
    }

    protected function transportEventDate(TransportRequest $transportRequest): string
    {
        return (string) $this->transportEvent($transportRequest)?->date?->isoFormat('LL');
    }

    /**
     * Transport tab of the race's entry page, where requests and offers are managed.
     */
    protected function transportPageUrl(TransportRequest $transportRequest): ?string
    {
        $event = $this->transportEvent($transportRequest);

        return $event !== null ? SportEventResource::getUrl('entry', ['record' => $event], panel: 'admin') : null;
    }

    /**
     * @param list<array{icon: string, label: string, value: string}> $facts
     * @return list<array{icon: string, label: string, value: string}>
     */
    protected function filledFacts(array $facts): array
    {
        return array_values(array_filter($facts, static fn (array $fact): bool => $fact['value'] !== ''));
    }

    /**
     * @return array{icon: string, label: string, value: string}
     */
    protected function directionFact(TransportRequest $transportRequest): array
    {
        return ['icon' => 'repeat-2', 'label' => __('transport.mail.club.direction_label'), 'value' => $transportRequest->direction->label()];
    }

    /**
     * @param string $labelKey key under transport.mail.club, e.g. seats_label or reserved_seats_label
     * @return array{icon: string, label: string, value: string}
     */
    protected function seatsFact(TransportRequest $transportRequest, string $labelKey = 'seats_label'): array
    {
        $seats = $transportRequest->seats;

        return ['icon' => 'users', 'label' => __('transport.mail.club.'.$labelKey), 'value' => trans_choice('transport.mail.club.seats_value', $seats, ['count' => $seats])];
    }

    /**
     * @param string $labelKey key under transport.mail.club, departure_label or departure_from_label
     * @return array{icon: string, label: string, value: string}
     */
    protected function departureFact(TransportRequest $transportRequest, string $labelKey = 'departure_label'): array
    {
        return ['icon' => 'map-pin', 'label' => __('transport.mail.club.'.$labelKey), 'value' => (string) $this->transportOffer($transportRequest)?->departure_place];
    }

    /**
     * @return array{icon: string, label: string, value: string}
     */
    protected function vehicleFact(TransportRequest $transportRequest): array
    {
        return ['icon' => 'car', 'label' => __('transport.mail.club.vehicle_label'), 'value' => (string) $this->transportOffer($transportRequest)?->vehicle?->name];
    }

    /**
     * @return array{icon: string, label: string, value: string}
     */
    protected function driverFact(TransportRequest $transportRequest): array
    {
        return ['icon' => 'user', 'label' => __('transport.mail.club.driver_label'), 'value' => (string) $this->transportOffer($transportRequest)?->user?->name];
    }

    /**
     * @return array{icon: string, label: string, value: string}
     */
    protected function passengerFact(TransportRequest $transportRequest): array
    {
        return ['icon' => 'user', 'label' => __('transport.mail.club.passenger_label'), 'value' => (string) $transportRequest->user?->name];
    }
}
