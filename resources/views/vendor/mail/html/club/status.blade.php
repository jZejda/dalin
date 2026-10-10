@props([
    'label',
    'tone' => 'positive',
])
@php
$brand = \App\Services\Mail\MailBranding::fromSettings();
$icon = $tone === 'negative' ? 'circle-x' : 'circle-check';
@endphp
<table class="club-status club-soft club-status-{{ $tone === 'negative' ? 'negative' : 'positive' }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<p class="club-status-label"><img class="club-icon" src="{{ $brand->iconUrl($icon) }}" width="18" height="18" alt="">{{ $label }}</p>
</td>
</tr>
</table>
