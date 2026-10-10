@props([
    'label',
    'tone' => 'positive',
])
{{ $tone === 'negative' ? '✗' : '✓' }} {{ $label }}
