@props([
    'url',
    'label',
    'secondaryUrl' => null,
    'secondaryLabel' => null,
    'secondaryTone' => 'default',
])
<table class="club-actions" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td><a href="{{ $url }}" class="club-button" target="_blank" rel="noopener">{{ $label }}</a></td>
@if (filled($secondaryUrl) && filled($secondaryLabel))
<td class="club-secondary{{ $secondaryTone === 'negative' ? ' club-secondary-negative' : '' }}"><a href="{{ $secondaryUrl }}" target="_blank" rel="noopener">{{ $secondaryLabel }}</a></td>
@endif
</tr>
</table>
