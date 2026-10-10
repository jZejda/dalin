@php
    /** @var string $variant new_account | reset */
    /** @var list<array{icon: string, label: string, value: string, wide?: bool}> $facts */
    $key = 'mail/user-password-send.club.'.$variant;
@endphp
<x-mail::club.message
    :eyebrow="__($key.'.eyebrow')"
    :title="__($key.'.title')"
    :lead="__($key.'.lead')"
>
<x-mail::club.facts :items="$facts" />

<x-mail::club.actions :url="$loginUrl" :label="__('mail/user-password-send.club.action')" :secondary-url="$helpUrl" :secondary-label="__($key.'.secondary')" />

<x-mail::club.note :label="__($key.'.note_label')">{{ __($key.'.note') }}</x-mail::club.note>

@if (filled($contactEmail))
<x-mail::club.fine>{{ __($key.'.fine', ['email' => $contactEmail]) }}</x-mail::club.fine>
@else
<x-mail::club.fine>{{ __($key.'.fine_no_contact') }}</x-mail::club.fine>
@endif
</x-mail::club.message>
