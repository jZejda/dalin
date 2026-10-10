@php
    /** @var list<array{icon: string, label: string, value: string}> $facts */
@endphp
<x-mail::club.message
    :eyebrow="__('transport.mail.offer_cancelled_club.eyebrow')"
    :title="__('transport.mail.offer_cancelled_club.title')"
    :lead="__('transport.mail.offer_cancelled_club.lead', ['event' => $eventName, 'date' => $eventDate])"
>
<x-mail::club.facts :items="$facts" />

@if (filled($transportUrl))
<x-mail::club.actions :url="$transportUrl" :label="__('transport.mail.offer_cancelled_club.action')" />

@endif
<x-mail::club.fine>{{ __('transport.mail.offer_cancelled_club.fine') }}</x-mail::club.fine>
</x-mail::club.message>
