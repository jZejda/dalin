@php
    /** @var list<array{name: string, email: string, amount: string}> $debtors */
@endphp
<x-mail::club.message
    :eyebrow="__('mail/users-in-debit.club.eyebrow')"
    :title="__('mail/users-in-debit.club.title')"
    :lead="__('mail/users-in-debit.club.lead', ['date' => $date])"
>
<x-mail::club.section :title="__('mail/users-in-debit.club.section')" />

@forelse ($debtors as $debtor)
<x-mail::club.person :name="$debtor['name']" :lines="[['text' => $debtor['email']]]" :amount="$debtor['amount']" />

@empty
{{ __('mail/users-in-debit.club.empty') }}

@endforelse
<x-mail::club.fine>{{ __('mail/users-in-debit.club.fine') }}</x-mail::club.fine>
</x-mail::club.message>
