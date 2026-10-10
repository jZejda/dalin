@props(['items'])
@php
$brand = \App\Services\Mail\MailBranding::fromSettings();
// Pair regular facts two per row; a fact with 'wide' => true (a password, a URL) gets a row of its own
$rows = [];
$pending = [];
foreach ($items as $item) {
    if ($item['wide'] ?? false) {
        if ($pending !== []) {
            $rows[] = $pending;
            $pending = [];
        }
        $rows[] = [$item];
        continue;
    }
    $pending[] = $item;
    if (count($pending) === 2) {
        $rows[] = $pending;
        $pending = [];
    }
}
if ($pending !== []) {
    $rows[] = $pending;
}
@endphp
<table class="club-facts club-soft" width="100%" cellpadding="0" cellspacing="0" role="presentation">
@foreach ($rows as $row)
<tr>
@foreach ($row as $item)
<td class="club-fact"@if ($item['wide'] ?? false) colspan="2" width="100%"@else width="50%"@endif valign="top">
<p class="club-meta">@if (filled($item['icon'] ?? null))<img class="club-icon" src="{{ $brand->iconUrl($item['icon']) }}" width="16" height="16" alt="">@endif{{ $item['label'] }}</p>
<p class="club-fact-value{{ ($item['wide'] ?? false) ? ' club-fact-value-break' : '' }}">{{ $item['value'] }}</p>
</td>
@endforeach
@if (count($row) === 1 && ! ($row[0]['wide'] ?? false))
<td class="club-fact club-fact-filler" width="50%">&nbsp;</td>
@endif
</tr>
@endforeach
</table>
