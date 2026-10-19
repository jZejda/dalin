@props([
    'amount',
    'breakdown' => null,
    'bankAccount' => null,
    'vs' => null,
    'fetchedAt' => null,
    'qrPng' => null,
    'qrName' => null,
    'mail' => null,
])
{{ __('mail/entry-ends-to-pay.payment.to_pay') }}: {{ $amount }}
@if (filled($breakdown))
{{ $breakdown }}
@endif
@if (filled($bankAccount))
{{ __('mail/entry-ends-to-pay.payment.bank_account') }}: {{ $bankAccount }}
@endif
@if (filled($vs))
{{ __('mail/entry-ends-to-pay.payment.vs') }}: {{ $vs }}
@endif
@if (filled($fetchedAt))
{{ __('mail/entry-ends-to-pay.payment.fetched_at', ['time' => $fetchedAt]) }}
@endif
