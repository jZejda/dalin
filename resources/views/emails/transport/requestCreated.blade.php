@php
    /** @var \App\Models\TransportRequest $transportRequest */
    $offer = $transportRequest->transportOffer;
    $event = $offer?->sportEvent;
    $passenger = (string) $transportRequest->user?->name;
    $seats = $transportRequest->seats;
    $facts = [
        ['icon' => 'repeat-2', 'label' => __('transport.direction'), 'value' => $transportRequest->direction->label()],
        ['icon' => 'users', 'label' => __('transport.seats'), 'value' => trans_choice('transport.mail.request_created_club.seats_value', $seats, ['count' => $seats])],
        ['icon' => 'map-pin', 'label' => __('transport.departure_place'), 'value' => (string) $offer?->departure_place],
        ['icon' => 'car', 'label' => __('transport.mail.request_created_club.vehicle_label'), 'value' => (string) $offer?->vehicle?->name],
    ];
@endphp
<x-mail::club.message
    :eyebrow="__('transport.mail.request_created_club.eyebrow')"
    :title="__('transport.mail.request_created_club.title', ['passenger' => $passenger])"
    :lead="trans_choice('transport.mail.request_created_club.lead', $seats, [
        'count' => $seats,
        'event' => (string) $event?->name,
        'date' => (string) $event?->date?->locale(app()->getLocale())->translatedFormat('j. F Y'),
    ])"
>
<x-mail::club.facts :items="$facts" />

@if (filled($transportRequest->note))
<x-mail::club.note :label="__('transport.mail.request_created_club.note_label', ['passenger' => $passenger])">{{ $transportRequest->note }}</x-mail::club.note>

@endif
<x-mail::club.actions :url="$approveUrl" :label="__('transport.mail.request_created_approve_button')" :secondary-url="$rejectUrl" :secondary-label="__('transport.mail.request_created_reject_button')" secondary-tone="negative" />

<x-mail::club.fine>{{ __('transport.mail.request_created_club.fine') }}</x-mail::club.fine>
</x-mail::club.message>
