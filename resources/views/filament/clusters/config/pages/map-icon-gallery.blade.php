<x-filament::page>
    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
        {{ __('map-icon-gallery.description') }}
    </p>

    {{-- Sports --}}
    <x-filament::section>
        <x-slot name="heading">{{ __('map-icon-gallery.sports_section') }}</x-slot>
        <x-slot name="description">{{ __('map-icon-gallery.sports_section_description') }}</x-slot>

        @if(empty($sports))
            <p class="text-sm text-gray-500">{{ __('map-icon-gallery.no_sports_yet') }}</p>
        @else
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($sports as $sport)
                    <div class="rounded-lg p-4 bg-gray-50 dark:bg-white/5 ring-1 ring-gray-950/5 dark:ring-white/10">
                        <div class="flex items-center justify-between mb-3">
                            <span class="font-semibold text-sm">{{ $sport['label'] }}</span>
                            <span class="text-xs font-mono text-gray-500">{{ $sport['color'] }}</span>
                        </div>
                        <div class="flex items-start justify-around">
                            @foreach($sport['variants'] as $variantLabel => $visual)
                                <div class="flex flex-col items-center gap-1.5">
                                    <x-map.marker-icon :visual="$visual"/>
                                    <span class="text-[11px] text-gray-500 text-center leading-tight">{{ $variantLabel }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>

    {{-- Categories --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">{{ __('map-icon-gallery.categories_section') }}</x-slot>
        <x-slot name="description">{{ __('map-icon-gallery.categories_section_description') }}</x-slot>

        <div class="grid sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($categories as $category)
                <div class="flex flex-col items-center gap-1.5 rounded-lg p-4 bg-gray-50 dark:bg-white/5 ring-1 ring-gray-950/5 dark:ring-white/10">
                    <x-map.marker-icon :visual="$category['visual']"/>
                    <span class="text-xs text-gray-600 dark:text-gray-400 text-center">{{ $category['label'] }}</span>
                </div>
            @endforeach
        </div>
    </x-filament::section>

    {{-- Auxiliary points --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">{{ __('map-icon-gallery.auxiliary_section') }}</x-slot>
        <x-slot name="description">{{ __('map-icon-gallery.auxiliary_section_description') }}</x-slot>

        <div class="grid sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($auxiliary as $marker)
                <div class="flex flex-col items-center gap-1.5 rounded-lg p-4 bg-gray-50 dark:bg-white/5 ring-1 ring-gray-950/5 dark:ring-white/10">
                    <x-map.marker-icon :visual="$marker['visual']"/>
                    <span class="text-xs text-gray-600 dark:text-gray-400 text-center">{{ $marker['label'] }}</span>
                </div>
            @endforeach
        </div>
    </x-filament::section>
</x-filament::page>
