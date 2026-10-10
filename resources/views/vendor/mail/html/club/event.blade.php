@props([
    'day',
    'month',
    'name',
    'url' => null,
    'meta' => null,
    'deadline' => null,
])
@php
$brand = \App\Services\Mail\MailBranding::fromSettings();
@endphp
<table class="club-event" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="club-event-cell" valign="top">
<table width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="club-date" width="50" valign="top"><span class="club-date-day">{{ $day }}</span><br>{{ $month }}</td>
<td class="club-event-content" valign="top">
<p class="club-event-name">@if (filled($url))<a href="{{ $url }}">{{ $name }}</a>@else{{ $name }}@endif</p>
@if (filled($meta))
<p class="club-meta"><img class="club-icon" src="{{ $brand->iconUrl('map-pin') }}" width="16" height="16" alt="">{{ $meta }}</p>
@endif
@if (filled($deadline))
<span class="club-pill">{{ $deadline }}</span>
@endif
</td>
</tr>
</table>
</td>
</tr>
</table>
