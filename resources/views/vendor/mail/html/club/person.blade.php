@props([
    'name',
    'detail' => null,
    'extra' => null,
    'lines' => [],
    'amount' => null,
])
@php
$brand = \App\Services\Mail\MailBranding::fromSettings();
@endphp
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="club-person">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td valign="top">
<p class="club-person-name">{{ $name }}@if (filled($detail)) &middot; {{ $detail }}@endif @if (filled($extra))<span class="club-person-extra">&nbsp; {{ $extra }}</span>@endif</p>
@foreach ($lines as $line)
<p class="club-meta">@if (filled($line['icon'] ?? null))<img class="club-icon" src="{{ $brand->iconUrl($line['icon']) }}" width="16" height="16" alt="">@endif{{ $line['text'] }}</p>
@endforeach
</td>
@if (filled($amount))
<td class="club-person-amount" align="right" valign="top">{{ $amount }}</td>
@endif
</tr>
</table>
</td>
</tr>
</table>
