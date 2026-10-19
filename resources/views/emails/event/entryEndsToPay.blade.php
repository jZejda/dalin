@php
    /** @var list<array{day: string, month: string, name: string, url: string, meta: string, deadline: string, payment: array{outstanding: bool, amount: string, breakdown: string, bank_account: string|null, vs: string|null, fetched_at: string, qr_png: string|null, qr_name: string}|null}> $events */
@endphp
<x-mail::club.message
    :eyebrow="__('mail/entry-ends-to-pay.club.eyebrow')"
    :title="__('mail/entry-ends-to-pay.club.title')"
    :lead="__('mail/entry-ends-to-pay.club.lead', ['term' => $term])"
>
<x-mail::club.section :title="$termTitle" />

@foreach ($events as $event)
<x-mail::club.event :day="$event['day']" :month="$event['month']" :name="$event['name']" :url="$event['url']" :meta="$event['meta']" :deadline="__('mail/common.club_layout.deadline', ['date' => $event['deadline']])" />

@if ($event['payment'] !== null && $event['payment']['outstanding'])
<x-mail::club.payment :amount="$event['payment']['amount']" :breakdown="$event['payment']['breakdown']" :bank-account="$event['payment']['bank_account']" :vs="$event['payment']['vs']" :fetched-at="$event['payment']['fetched_at']" :qr-png="$event['payment']['qr_png']" :qr-name="$event['payment']['qr_name']" :mail="$message ?? null" />

@elseif ($event['payment'] !== null)
<x-mail::club.status :label="__('mail/entry-ends-to-pay.payment.paid', ['amount' => $event['payment']['amount']])" />

@endif
@endforeach
<x-mail::club.fine>{{ $hasPayments ? __('mail/entry-ends-to-pay.club.fine_with_payments') : __('mail/entry-ends-to-pay.club.fine') }}</x-mail::club.fine>
</x-mail::club.message>
