@php
    /** @var list<array{icon: string, label: string, value: string}> $facts */
    /** @var string $content Markdown written in the admin panel, escaped before parsing */
@endphp
<x-mail::club.message
    :eyebrow="__('mail/user-app-notification.club.eyebrow')"
    :title="__('mail/user-app-notification.club.title')"
    :lead="__('mail/user-app-notification.club.lead')"
>
<x-mail::club.facts :items="$facts" />

<x-mail::club.heading :title="__('mail/user-app-notification.club.heading')" />

{{ $content }}

</x-mail::club.message>
