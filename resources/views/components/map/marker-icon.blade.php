@php
    /** @var \App\Services\Map\MapMarkerVisual $visual */
@endphp
{{--
    Classic teardrop pin, built with the standard CSS trick: a square with three
    rounded corners and one square corner, rotated -45deg so the square corner
    becomes the point at the bottom. The icon itself lives in a separate,
    non-rotated sibling layered on top, so it doesn't need counter-rotating.

    Geometry (must stay in sync with iconSize/iconAnchor/popupAnchor in
    leaflet-map-widget.blade.php): a 31x31 head offset by (3,3) inside a 37x41
    box puts the pin's tip at (~19,40) — that's the GPS anchor point.
    (1.3x the original 24/2/28x32/14,31 — scale both together if resized again.)
--}}
<div style="position:relative;width:37px;height:41px;">
    <div style="position:absolute;left:3px;top:3px;width:31px;height:31px;box-sizing:border-box;background-color:{{ $visual->colorHex }};border:2px solid #ffffff;border-radius:50% 50% 50% 0;transform:rotate(-45deg);box-shadow:0 1px 4px rgba(0,0,0,.5);"></div>
    <div style="position:absolute;left:3px;top:3px;width:31px;height:31px;display:flex;align-items:center;justify-content:center;color:#ffffff;">
        @include('components.map.icons.' . $visual->iconSlug)
    </div>
    @if($visual->categoryIconSlug !== null)
        <div style="position:absolute;top:-3px;right:-3px;width:18px;height:18px;border-radius:9999px;background:#ffffff;display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 1px rgba(0,0,0,.25);z-index:2;">
            @include('components.map.icons.category-' . $visual->categoryIconSlug)
        </div>
    @endif
    @if($visual->modifierLetter !== null)
        <div style="position:absolute;top:-3px;left:-3px;min-width:18px;height:18px;padding:0 3px;border-radius:9999px;background:#111827;color:#ffffff;font-size:12px;font-weight:700;line-height:18px;text-align:center;border:1px solid #ffffff;z-index:2;">{{ $visual->modifierLetter }}</div>
    @endif
</div>
