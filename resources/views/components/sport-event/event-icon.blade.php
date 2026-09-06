<div>
    <span
        class="text-sm font-semibold inline-flex items-center p-1.5 rounded-full relative"
        style="background-color: {{ $colorHex }}1a; color: {{ $colorHex }};"
    >
        @include('components.map.icons.' . $iconSlug)

        @if($categoryIconSlug !== null)
            <span style="position:absolute;top:-3px;right:-3px;width:12px;height:12px;border-radius:9999px;background:#ffffff;display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 1px rgba(0,0,0,.15);">
                @include('components.map.icons.category-' . $categoryIconSlug)
            </span>
        @endif

        <span class="sr-only">{{ __('sport-event.type_enum.' . $eventType->value) }}</span>
    </span>
</div>
