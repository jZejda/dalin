@props([
    'day',
    'month',
    'name',
    'url' => null,
    'meta' => null,
    'deadline' => null,
])
{{ $day }}. {{ $month }} – {{ $name }}
@if (filled($meta))
{{ $meta }}
@endif
@if (filled($deadline))
{{ $deadline }}
@endif
@if (filled($url))
{{ $url }}
@endif
