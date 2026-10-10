@props([
    'name',
    'detail' => null,
    'extra' => null,
    'lines' => [],
    'amount' => null,
])
{{ $name }}@if (filled($detail)) · {{ $detail }}@endif @if (filled($extra))({{ $extra }})@endif @if (filled($amount))– {{ $amount }}@endif

@foreach ($lines as $line)
{{ $line['text'] }}
@endforeach
