@props([
    'name',
    'detail' => null,
    'extra' => null,
    'lines' => [],
])
@php
$brand = \App\Services\Mail\MailBranding::fromSettings();
@endphp
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="club-person">
<p class="club-person-name">{{ $name }}@if (filled($detail)) &middot; {{ $detail }}@endif @if (filled($extra))<span class="club-person-extra">&nbsp; {{ $extra }}</span>@endif</p>
@foreach ($lines as $line)
<p class="club-meta"><img class="club-icon" src="{{ $brand->iconUrl($line['icon']) }}" width="16" height="16" alt="">{{ $line['text'] }}</p>
@endforeach
</td>
</tr>
</table>
