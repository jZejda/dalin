@php
    /** @var bool $isDebit */
    /** @var list<array{icon: string, label: string, value: string}> $facts */
@endphp
<x-mail::club.message
    :eyebrow="__('mail/user-credit-change.club.eyebrow')"
    :title="__($isDebit ? 'mail/user-credit-change.club.title_debit' : 'mail/user-credit-change.club.title_credit', ['amount' => $amount])"
    :lead="__('mail/user-credit-change.club.lead')"
>
<x-mail::club.balance :label="__('mail/user-credit-change.club.balance_label')" :amount="$balance" :note="__('mail/user-credit-change.club.balance_note', ['date' => $balanceDate])" />

<x-mail::club.facts :items="$facts" />

@if (filled($contactEmail))
<x-mail::club.fine>{{ __('mail/user-credit-change.club.fine', ['email' => $contactEmail]) }}</x-mail::club.fine>
@else
<x-mail::club.fine>{{ __('mail/user-credit-change.club.fine_no_contact') }}</x-mail::club.fine>
@endif
</x-mail::club.message>
