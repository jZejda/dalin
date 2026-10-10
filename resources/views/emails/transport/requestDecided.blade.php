@php
    /** @var bool $approved */
    /** @var int $seats */
    /** @var list<array{icon: string, label: string, value: string}> $facts */
    $key = $approved ? 'transport.mail.request_approved_club' : 'transport.mail.request_rejected_club';
@endphp
<x-mail::club.message
    :eyebrow="__($key.'.eyebrow')"
    :title="__($key.'.title')"
    :lead="__($key.'.lead', ['event' => $eventName, 'date' => $eventDate])"
>
<x-mail::club.status
    :tone="$approved ? 'positive' : 'negative'"
    :label="$approved ? trans_choice($key.'.status', $seats, ['count' => $seats]) : __($key.'.status')"
/>

<x-mail::club.facts :items="$facts" />

@if (filled($transportUrl))
<x-mail::club.actions :url="$transportUrl" :label="__($key.'.action')" />

@endif
<x-mail::club.fine>{{ __($key.'.fine') }}</x-mail::club.fine>
</x-mail::club.message>
