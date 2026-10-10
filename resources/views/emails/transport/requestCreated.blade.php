@php
    /** @var string $passenger */
    /** @var int $seats */
    /** @var list<array{icon: string, label: string, value: string}> $facts */
@endphp
<x-mail::club.message
    :eyebrow="__('transport.mail.request_created_club.eyebrow')"
    :title="__('transport.mail.request_created_club.title', ['passenger' => $passenger])"
    :lead="trans_choice('transport.mail.request_created_club.lead', $seats, ['count' => $seats, 'event' => $eventName, 'date' => $eventDate])"
>
<x-mail::club.facts :items="$facts" />

@if (filled($note))
<x-mail::club.note :label="__('transport.mail.request_created_club.note_label')" quoted>{{ $note }}</x-mail::club.note>

@endif
<x-mail::club.actions :url="$approveUrl" :label="__('transport.mail.request_created_approve_button')" :secondary-url="$rejectUrl" :secondary-label="__('transport.mail.request_created_reject_button')" secondary-tone="negative" />

<x-mail::club.fine>{{ __('transport.mail.request_created_club.fine') }}</x-mail::club.fine>
</x-mail::club.message>
