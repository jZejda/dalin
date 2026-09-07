@php
    /** @var \App\Services\Map\MapMarkerVisual $visual */
    /** @var string $label */
@endphp
<div style="display:flex;align-items:center;gap:8px;">
    <x-map.marker-icon :visual="$visual" />
    <span>{{ $label }}</span>
</div>
