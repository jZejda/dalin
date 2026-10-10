@props(['items'])
@php
$brand = \App\Services\Mail\MailBranding::fromSettings();
@endphp
<table class="club-facts club-soft" width="100%" cellpadding="0" cellspacing="0" role="presentation">
@foreach (array_chunk($items, 2) as $row)
<tr>
@foreach ($row as $item)
<td class="club-fact" width="50%" valign="top">
<p class="club-meta">@if (filled($item['icon'] ?? null))<img class="club-icon" src="{{ $brand->iconUrl($item['icon']) }}" width="16" height="16" alt="">@endif{{ $item['label'] }}</p>
<p class="club-fact-value">{{ $item['value'] }}</p>
</td>
@endforeach
@if (count($row) === 1)
<td class="club-fact club-fact-filler" width="50%">&nbsp;</td>
@endif
</tr>
@endforeach
</table>
