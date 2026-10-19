@props([
    'amount',
    'breakdown' => null,
    'bankAccount' => null,
    'vs' => null,
    'fetchedAt' => null,
    'qrPng' => null,
    'qrName' => 'qr-platba.png',
    // Symfony message of the mail being sent (null when rendered for preview/tests) — the QR is an inline attachment
    'mail' => null,
])
@php
$brand = \App\Services\Mail\MailBranding::fromSettings();
$qrSrc = filled($qrPng) && $mail instanceof \Illuminate\Mail\Message ? $mail->embedData($qrPng, $qrName, 'image/png') : null;
@endphp
<table class="club-payment club-soft" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="club-payment-details" valign="top">
<p class="club-balance-label">{{ __('mail/entry-ends-to-pay.payment.to_pay') }}</p>
<p class="club-payment-amount">{{ $amount }}</p>
@if (filled($breakdown))
<p class="club-balance-note">{{ $breakdown }}</p>
@endif
@if (filled($bankAccount))
<p class="club-meta"><img class="club-icon" src="{{ $brand->iconUrl('landmark') }}" width="16" height="16" alt="">{{ __('mail/entry-ends-to-pay.payment.bank_account') }} <strong class="club-payment-value">{{ $bankAccount }}</strong></p>
@endif
@if (filled($vs))
<p class="club-meta"><img class="club-icon" src="{{ $brand->iconUrl('hash') }}" width="16" height="16" alt="">{{ __('mail/entry-ends-to-pay.payment.vs') }} <strong class="club-payment-value">{{ $vs }}</strong></p>
@endif
@if (filled($fetchedAt))
<p class="club-balance-note">{{ __('mail/entry-ends-to-pay.payment.fetched_at', ['time' => $fetchedAt]) }}</p>
@endif
</td>
@if ($qrSrc !== null)
<td class="club-payment-qr" width="132" valign="top" align="center">
<img src="{{ $qrSrc }}" width="120" height="120" alt="{{ __('mail/entry-ends-to-pay.payment.qr_alt') }}">
<p class="club-balance-note">{{ __('mail/entry-ends-to-pay.payment.qr_caption') }}</p>
</td>
@endif
</tr>
</table>
