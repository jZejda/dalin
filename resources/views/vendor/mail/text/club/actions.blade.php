@props([
    'url',
    'label',
    'secondaryUrl' => null,
    'secondaryLabel' => null,
    'secondaryTone' => 'default',
])
{{ $label }}: {{ $url }}
@if (filled($secondaryUrl) && filled($secondaryLabel))
{{ $secondaryLabel }}: {{ $secondaryUrl }}
@endif
