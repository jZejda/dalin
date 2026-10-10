@php
    /** @var list<array{icon: string, label: string, value: string}> $facts */
    /** @var string $content Markdown written by the organiser, escaped before parsing */
@endphp
<x-mail::club.message
    :eyebrow="__('mail/user-entry-notification.club.eyebrow')"
    :title="__('mail/user-entry-notification.club.title')"
    :lead="$eventTitle"
>
@if ($facts !== [])
<x-mail::club.facts :items="$facts" />

@endif
<x-mail::club.heading :title="__('mail/user-entry-notification.club.heading')" />

{{ $content }}

</x-mail::club.message>
