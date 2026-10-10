@props([
    'source',
    'colored' => false,
])
@php
    /** @var \App\Enums\SportEventLinkSource $source */
    $icon = $colored ? $source->getColorIcon() : $source->getIcon();
@endphp
{{-- Square icon of a link's destination (ORIS, OResults, …); decorative, the label is in the title. --}}
<span {{ $attributes->class(['inline-flex size-4 shrink-0']) }} title="{{ $source->getLabel() }}">
    @svg($icon, 'size-full', ['aria-hidden' => 'true'])
</span>
