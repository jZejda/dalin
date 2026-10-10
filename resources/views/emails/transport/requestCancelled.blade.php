@php
    /** @var string $passenger */
    /** @var int $seats */
    /** @var list<array{icon: string, label: string, value: string}> $facts */
@endphp
<x-mail::club.message
    :eyebrow="__('transport.mail.request_cancelled_club.eyebrow')"
    :title="__('transport.mail.request_cancelled_club.title', ['passenger' => $passenger])"
    :lead="trans_choice('transport.mail.request_cancelled_club.lead', $seats, ['count' => $seats, 'event' => $eventName, 'date' => $eventDate])"
>
<x-mail::club.facts :items="$facts" />

@if (filled($transportUrl))
<x-mail::club.actions :url="$transportUrl" :label="__('transport.mail.request_cancelled_club.action')" />

@endif
<x-mail::club.fine>{{ __('transport.mail.request_cancelled_club.fine') }}</x-mail::club.fine>
</x-mail::club.message>
