@props([
    'name',
    'detail' => null,
    'extra' => null,
    'lines' => [],
])
{{ $name }}@if (filled($detail)) · {{ $detail }}@endif @if (filled($extra))({{ $extra }})@endif

@foreach ($lines as $line)
{{ $line['text'] }}
@endforeach
