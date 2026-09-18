    @if($hasMap)
    <section id="event-map" class="min-w-0 border-b border-terrain-line pb-8">
        <header class="mb-5 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold leading-none tracking-tight text-terrain-ink">{{ __('sport-event.public.map_title') }}</h2>
                @if($hasMarkers)
                    <p class="mt-1.5 text-sm text-terrain-secondary">{{ __('sport-event.public.map_description') }}</p>
                @endif
            </div>
            <a href="https://www.google.com/maps?q={{ $event->gps_lat }},{{ $event->gps_lon }}"
               target="_blank" rel="noopener noreferrer"
               class="inline-flex min-h-11 items-center gap-1.5 rounded-terrain-control border border-terrain-line bg-terrain-surface px-3 py-1.5 text-xs font-medium text-terrain-ink transition-colors hover:bg-terrain-muted hover:text-terrain-ink"
               >
                {{ __('sport-event.public.open_in_maps') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </a>
        </header>
        <div class="relative isolate overflow-hidden rounded-terrain-panel border border-terrain-line" role="region" aria-label="{{ __('sport-event.public.map_title') }}">
        @livewire(\App\Livewire\Shared\Maps\LeafletMap::class, [
            'sportEvent' => $event,
            'publicMap'  => true,
        ])
        </div>

        {{-- Body zájmu --}}
        @if($hasMarkers)
        <div class="border-t border-terrain-line p-5 md:p-6">
            <h3 class="mb-4 text-sm font-semibold text-terrain-ink">{{ __('sport-event.public.markers_title') }}</h3>
            <ul class="grid grid-cols-1 gap-3 md:grid-cols-2">
                @foreach($event->sportEventMarkers->sortBy('letter') as $marker)
                    <li class="flex items-start gap-3 rounded-terrain-control border border-terrain-line p-3">
                        <div class="shrink-0">
                            <x-map.marker-icon :visual="$markerResolver->resolveForMarker($marker, $event)" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-medium text-terrain-ink">{{ $marker->label }}</span>
                                @if($marker->type)
                                    <span class="inline-flex items-center rounded-terrain-control bg-terrain-muted px-2 py-0.5 text-xs font-medium text-terrain-secondary">
                                        {{ __('sport-event.type_enum_markers.' . $marker->type->value) }}
                                    </span>
                                @endif
                            </div>
                            @if($marker->desc)
                                <p class="mt-0.5 text-xs text-terrain-secondary">{{ $marker->desc }}</p>
                            @endif
                        </div>
                        <a href="https://www.google.com/maps?q={{ $marker->lat }},{{ $marker->lon }}"
                           target="_blank" rel="noopener noreferrer"
                           aria-label="{{ $marker->label }}: {{ __('sport-event.public.open_in_maps') }}" class="inline-flex size-11 shrink-0 items-center justify-center text-terrain-secondary transition-colors hover:text-terrain-warning"
                           title="{{ __('sport-event.public.open_in_maps') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0 1 15 0Z" />
                            </svg>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
        @endif
    </section>
    @endif

