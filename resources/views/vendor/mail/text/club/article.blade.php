@props([
    'title',
    'html' => null,
    'text' => '',
    'separated' => false,
])
@if ($separated)
---

@endif
{{ $title }}

{{ $text }}
