@props([
    'label',
    'amount',
    'note' => null,
])
{{ $label }}: {{ $amount }}
@if (filled($note))
{{ $note }}
@endif
