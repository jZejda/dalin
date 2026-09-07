@php
    use App\Enums\SportEventTransportType;
    use App\Models\SportEvent;
    use App\Services\Map\MapMarkerResolver;
    use App\Services\OrisApiService;
    use App\Shared\Helpers\AppHelper;
    use Carbon\Carbon;

    /** @var SportEvent $event */
    /** @var int $activeEntriesCount */
    /** @var int $transportOffersCount */
    /** @var int $transportFreeSeats */

    $hasMap        = $event->gps_lat && $event->gps_lon;
    $hasMarkers    = $event->sportEventMarkers->count() > 0;
    $markerResolver = new MapMarkerResolver();
    $hasCategories = $event->sportClasses->count() > 0;
    $hasNews       = $event->sportEventNews->count() > 0;
    $hasLinks      = $event->sportEventLinks->count() > 0;
    $hasServices   = $event->sportServices->count() > 0;
    $hasWeather    = !is_null($event->weather);
    $hasTransport  = $event->transport_type !== SportEventTransportType::None;

    $entryDates = collect([
        1 => $event->entry_date_1,
        2 => $event->entry_date_2,
        3 => $event->entry_date_3,
    ])->filter();

    $nextDeadline    = $entryDates->first(fn ($date) => Carbon::parse($date)->isFuture());
    $nextDeadlineNum = $entryDates->search($nextDeadline);
@endphp

@extends('layouts.app')

@section('title', $event->name)
@section('sponsorSectionId', $sponsorSectionId)

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     HERO
     ═══════════════════════════════════════════════════════════ --}}
<div class="border-b border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="container mx-auto px-4 py-6 md:py-8">

        {{-- Breadcrumb --}}
        <nav class="mb-4 flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
            <a href="/" class="transition-colors hover:text-gray-900 dark:hover:text-gray-100">{{ __('sport-event.public.breadcrumb_home') }}</a>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
            <a href="/" class="transition-colors hover:text-gray-900 dark:hover:text-gray-100">{{ __('sport-event.public.breadcrumb_events') }}</a>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
            <span class="truncate font-medium text-gray-900 dark:text-gray-100">{{ $event->name }}</span>
        </nav>

        {{-- Badges --}}
        <div class="mb-3 flex flex-wrap items-center gap-2">
            @if($event->cancelled)
                <span class="inline-flex items-center gap-1 rounded-md bg-red-600 px-2.5 py-0.5 text-xs font-semibold text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                    {{ __('sport-event.public.cancelled_badge') }}
                </span>
            @endif
            <span class="inline-flex items-center rounded-md border border-orange-500/30 bg-orange-500/10 px-2.5 py-0.5 text-xs font-semibold text-orange-700 dark:text-orange-400">
                {{ __('sport-event.type_enum.' . $event->event_type->value) }}
            </span>
            @if($event->sportDiscipline)
                <span class="inline-flex items-center rounded-md border border-gray-200 bg-gray-50 px-2.5 py-0.5 text-xs font-semibold text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $event->sportDiscipline->short_name }}
                </span>
            @endif
            @if($event->sportLevel)
                <span class="inline-flex items-center rounded-md border border-gray-200 bg-gray-50 px-2.5 py-0.5 text-xs font-semibold text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                    {{ $event->sportLevel->short_name }}
                </span>
            @endif
            @if($event->oris_id)
                <a href="{{ OrisApiService::ORIS_URL }}/Zavod?id={{ $event->oris_id }}"
                   target="_blank"
                   class="inline-flex items-center gap-1 rounded-md border border-gray-200 bg-gray-50 px-2.5 py-0.5 text-xs font-semibold text-gray-700 transition-colors hover:border-orange-500/40 hover:text-orange-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:text-orange-400"
                   style="text-decoration: none !important;">
                    ORIS {{ $event->oris_id }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                </a>
            @endif
        </div>

        {{-- Title --}}
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 md:text-4xl dark:text-gray-50">
            {{ $event->name }}
        </h1>
        @if($event->alt_name)
            <p class="mt-1.5 text-base text-gray-500 dark:text-gray-400">{{ $event->alt_name }}</p>
        @endif

        {{-- Meta row --}}
        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-sm text-gray-600 dark:text-gray-400">
            <span class="flex items-center gap-1.5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                {{ $event->date->format(AppHelper::DATE_FORMAT) }}
                @if($event->date_end && $event->date_end->gt($event->date))
                    — {{ $event->date_end->format(AppHelper::DATE_FORMAT) }}
                @endif
            </span>
            @if($event->start_time)
                <span class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ $event->start_time }}
                </span>
            @endif
            @if($event->place)
                <span class="flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0 1 15 0Z" />
                    </svg>
                    {{ $event->place }}
                </span>
            @endif
        </div>

    </div>
</div>
<div class="h-0.5 bg-orange-500"></div>

{{-- ═══════════════════════════════════════════════════════════
     OBSAH
     ═══════════════════════════════════════════════════════════ --}}
<div class="container mx-auto space-y-6 px-4 py-6 md:py-8">

    {{-- Alerts --}}
    @if($event->cancelled)
        <div class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 dark:border-red-900/50 dark:bg-red-950/40">
            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <div>
                <div class="text-sm font-semibold text-red-800 dark:text-red-300">{{ __('sport-event.public.cancelled_title') }}</div>
                @if($event->cancelled_reason)
                    <div class="mt-0.5 text-sm text-red-700 dark:text-red-400">{{ $event->cancelled_reason }}</div>
                @endif
            </div>
        </div>
    @endif

    @if($event->event_warning)
        <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-900/50 dark:bg-amber-950/40">
            <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <div class="text-sm text-amber-800 dark:text-amber-200">{!! $event->event_warning !!}</div>
        </div>
    @endif

    {{-- ─────────────────────────────────────────────
         STAT KARTY — přehled na první pohled
         ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">

        {{-- Přihlášky (anonymizovaně) --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('sport-event.public.stats_entries') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                </svg>
            </div>
            <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-50">{{ $activeEntriesCount }}</div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ trans_choice('sport-event.public.stats_entries_hint', $activeEntriesCount) }}</p>
        </div>

        {{-- Kategorie --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('sport-event.public.stats_classes') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z" />
                </svg>
            </div>
            <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-50">{{ $event->sportClasses->count() }}</div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ trans_choice('sport-event.public.stats_classes_hint', $event->sportClasses->count()) }}</p>
        </div>

        {{-- Sdílená doprava --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('sport-event.public.stats_transport') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                </svg>
            </div>
            @if($hasTransport && $transportOffersCount > 0)
                <div class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-50">{{ $transportOffersCount }}</div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ trans_choice('sport-event.public.stats_transport_hint', $transportFreeSeats, ['count' => $transportFreeSeats]) }}</p>
            @elseif($hasTransport)
                <div class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-50">{{ __('sport-event.transport_type_enum.' . $event->transport_type->value) }}</div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('sport-event.public.stats_transport_no_offers') }}</p>
            @else
                <div class="mt-2 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('sport-event.transport_type_enum.' . $event->transport_type->value) }}</div>
            @endif
        </div>

        {{-- Uzávěrka přihlášek --}}
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('sport-event.public.stats_deadline') }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0 text-orange-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            @if($nextDeadline)
                <div class="mt-2 text-lg font-bold text-gray-900 dark:text-gray-50">{{ Carbon::parse($nextDeadline)->format(AppHelper::DATE_FORMAT) }}</div>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('sport-event.public.stats_deadline_term', ['term' => $nextDeadlineNum]) }}
                    · {{ Carbon::parse($nextDeadline)->format('H:i') }}
                </p>
            @elseif($entryDates->isNotEmpty())
                <div class="mt-2 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('sport-event.public.stats_deadline_passed') }}</div>
            @else
                <div class="mt-2 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('sport-event.public.stats_deadline_none') }}</div>
            @endif
        </div>

    </div>

    {{-- ─────────────────────────────────────────────
         MAPA + BODY ZÁJMU
         ───────────────────────────────────────────── --}}
    @if($hasMap)
    <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
        <header class="flex items-center justify-between p-5 md:p-6">
            <div>
                <h2 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-50">{{ __('sport-event.public.map_title') }}</h2>
                @if($hasMarkers)
                    <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{{ __('sport-event.public.map_description') }}</p>
                @endif
            </div>
            <a href="https://www.google.com/maps?q={{ $event->gps_lat }},{{ $event->gps_lon }}"
               target="_blank"
               class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm transition-colors hover:bg-gray-50 hover:text-gray-900 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
               style="text-decoration: none !important;">
                {{ __('sport-event.public.open_in_maps') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                </svg>
            </a>
        </header>
        @livewire(\App\Livewire\Shared\Maps\LeafletMap::class, [
            'sportEvent' => $event,
            'publicMap'  => true,
        ])

        {{-- Body zájmu --}}
        @if($hasMarkers)
        <div class="border-t border-gray-200 p-5 md:p-6 dark:border-gray-800">
            <h3 class="mb-4 text-sm font-semibold text-gray-900 dark:text-gray-50">{{ __('sport-event.public.markers_title') }}</h3>
            <ul class="grid grid-cols-1 gap-3 md:grid-cols-2">
                @foreach($event->sportEventMarkers->sortBy('letter') as $marker)
                    <li class="flex items-start gap-3 rounded-lg border border-gray-100 p-3 dark:border-gray-800">
                        <div class="shrink-0">
                            <x-map.marker-icon :visual="$markerResolver->resolveForMarker($marker, $event)" />
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $marker->label }}</span>
                                @if($marker->type)
                                    <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
                                        {{ __('sport-event.type_enum_markers.' . $marker->type->value) }}
                                    </span>
                                @endif
                            </div>
                            @if($marker->desc)
                                <p class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ $marker->desc }}</p>
                            @endif
                        </div>
                        <a href="https://www.google.com/maps?q={{ $marker->lat }},{{ $marker->lon }}"
                           target="_blank"
                           class="shrink-0 text-gray-400 transition-colors hover:text-orange-600 dark:hover:text-orange-400"
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

    {{-- ─────────────────────────────────────────────
         HLAVNÍ GRID — obsah 2/3 + sidebar 1/3
         ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- ═══ HLAVNÍ SLOUPEC ═══ --}}
        <div class="space-y-6 lg:col-span-2">

            {{-- Informace o akci --}}
            @if($event->event_info)
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm md:p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-50">{{ __('sport-event.public.info_title') }}</h2>
                <div class="prose mt-4 max-w-none text-sm dark:prose-invert">{!! $event->event_info !!}</div>
            </section>
            @endif

            {{-- Přihlášky --}}
            @if($event->entry_desc || $entryDates->isNotEmpty())
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm md:p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-50">{{ __('sport-event.public.entry_title') }}</h2>
                        <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">
                            {{ trans_choice('sport-event.public.entry_registered', $activeEntriesCount, ['count' => $activeEntriesCount]) }}
                        </p>
                    </div>
                    <span class="inline-flex items-center rounded-md border border-orange-500/30 bg-orange-500/10 px-2.5 py-0.5 text-xs font-semibold text-orange-700 dark:text-orange-400">
                        {{ $activeEntriesCount }}
                    </span>
                </div>

                @if($entryDates->isNotEmpty())
                <ol class="mt-4 space-y-2">
                    @foreach($entryDates as $termNumber => $entryDate)
                        @php
                            $isPast   = Carbon::parse($entryDate)->isPast();
                            $isNext   = $termNumber === $nextDeadlineNum;
                            $increase = $termNumber === 2 ? $event->increase_entry_fee_2 : ($termNumber === 3 ? $event->increase_entry_fee_3 : null);
                        @endphp
                        <li class="flex items-center justify-between rounded-lg border p-3 text-sm {{ $isNext ? 'border-orange-500/40 bg-orange-500/5' : 'border-gray-100 dark:border-gray-800' }}">
                            <span class="flex items-center gap-2 font-medium {{ $isPast ? 'text-gray-400 line-through dark:text-gray-500' : 'text-gray-900 dark:text-gray-100' }}">
                                @if($isNext)
                                    <span class="h-2 w-2 shrink-0 rounded-full bg-orange-500"></span>
                                @endif
                                {{ __('sport-event.public.stats_deadline_term', ['term' => $termNumber]) }}
                            </span>
                            <span class="flex items-center gap-2">
                                @if($increase)
                                    <span class="text-xs font-medium text-orange-600 dark:text-orange-400">+{{ $increase }} Kč</span>
                                @endif
                                <span class="tabular-nums {{ $isPast ? 'text-gray-400 line-through dark:text-gray-500' : 'text-gray-700 dark:text-gray-300' }}">
                                    {{ Carbon::parse($entryDate)->format(AppHelper::DATE_TIME_FORMAT) }}
                                </span>
                            </span>
                        </li>
                    @endforeach
                </ol>
                @endif

                @if($event->entry_desc)
                    <div class="prose mt-4 max-w-none text-sm dark:prose-invert">{!! $event->entry_desc !!}</div>
                @endif
            </section>
            @endif

            {{-- Kategorie --}}
            @if($hasCategories)
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm md:p-6 dark:border-gray-800 dark:bg-gray-900">
                <div class="flex items-baseline justify-between">
                    <h2 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-50">{{ __('sport-event.public.classes_title') }}</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ trans_choice('sport-event.public.classes_count', $event->sportClasses->count(), ['count' => $event->sportClasses->count()]) }}</span>
                </div>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach($event->sportClasses->sortBy('name') as $class)
                        <span class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 bg-gray-50 px-2.5 py-1 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300">
                            <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-orange-500"></span>
                            {{ $class->name }}
                        </span>
                    @endforeach
                </div>
            </section>
            @endif

            {{-- Služby --}}
            @if($hasServices)
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <header class="p-5 pb-3 md:p-6 md:pb-3">
                    <h2 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-50">{{ __('sport-event.public.services_title') }}</h2>
                </header>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-200 dark:border-gray-800">
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-500 md:px-6 dark:text-gray-400">{{ __('sport-event.public.services_name') }}</th>
                                <th class="px-5 py-2.5 text-right text-xs font-medium text-gray-500 md:px-6 dark:text-gray-400">{{ __('sport-event.public.services_price') }}</th>
                                <th class="px-5 py-2.5 text-right text-xs font-medium text-gray-500 md:px-6 dark:text-gray-400">{{ __('sport-event.public.services_available') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($event->sportServices as $service)
                                <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ !$loop->last ? 'border-b border-gray-100 dark:border-gray-800' : '' }}">
                                    <td class="px-5 py-3 font-medium text-gray-900 md:px-6 dark:text-gray-100">{{ $service->service_name_cz }}</td>
                                    <td class="px-5 py-3 text-right tabular-nums text-gray-600 md:px-6 dark:text-gray-400">{{ $service->unit_price }} Kč</td>
                                    <td class="px-5 py-3 text-right tabular-nums text-gray-600 md:px-6 dark:text-gray-400">{{ $service->qty_remaining ?? $service->qty_available ?? '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
            @endif

            {{-- Novinky --}}
            @if($hasNews)
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <header class="flex items-baseline justify-between p-5 pb-3 md:p-6 md:pb-3">
                    <h2 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-50">{{ __('sport-event.public.news_title') }}</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $event->sportEventNews->count() }}</span>
                </header>
                <ol class="px-5 pb-5 md:px-6 md:pb-6">
                    @foreach($event->sportEventNews->sortByDesc('date') as $i => $news)
                        <li class="relative flex gap-4 {{ !$loop->last ? 'pb-5' : '' }}">
                            {{-- Timeline --}}
                            <div class="flex flex-col items-center">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $loop->first ? 'bg-orange-500' : 'bg-gray-300 dark:bg-gray-600' }}"></span>
                                @if(!$loop->last)
                                    <span class="mt-1 w-px flex-1 bg-gray-200 dark:bg-gray-700"></span>
                                @endif
                            </div>
                            <div class="min-w-0 flex-1 pb-1">
                                <time class="text-xs font-medium {{ $loop->first ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-gray-400' }}">
                                    {{ Carbon::parse($news->date)->format(AppHelper::DATE_FORMAT) }}
                                </time>
                                <div class="mt-1 text-sm leading-relaxed text-gray-800 dark:text-gray-200">{!! $news->text !!}</div>
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>
            @endif

        </div>

        {{-- ═══ SIDEBAR ═══ --}}
        <div class="space-y-6">

            {{-- Základní informace --}}
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm md:p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-50">{{ __('sport-event.public.basic_title') }}</h2>
                <dl class="mt-4 space-y-3 text-sm">

                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-gray-500 dark:text-gray-400">{{ __('sport-event.public.label_date') }}</dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">
                            {{ $event->date->format(AppHelper::DATE_FORMAT) }}
                            @if($event->date_end && $event->date_end->gt($event->date))
                                — {{ $event->date_end->format(AppHelper::DATE_FORMAT) }}
                            @endif
                        </dd>
                    </div>

                    @if($event->start_time)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-gray-500 dark:text-gray-400">{{ __('sport-event.public.label_start') }}</dt>
                        <dd class="text-right font-medium tabular-nums text-gray-900 dark:text-gray-100">{{ $event->start_time }}</dd>
                    </div>
                    @endif

                    @if($event->place)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-gray-500 dark:text-gray-400">{{ __('sport-event.public.label_place') }}</dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">{{ $event->place }}</dd>
                    </div>
                    @endif

                    @if($hasMap)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-gray-500 dark:text-gray-400">{{ __('sport-event.public.label_gps') }}</dt>
                        <dd class="text-right">
                            <a href="https://www.google.com/maps?q={{ $event->gps_lat }},{{ $event->gps_lon }}"
                               target="_blank"
                               class="font-medium tabular-nums text-gray-700 transition-colors hover:text-orange-600 dark:text-gray-300 dark:hover:text-orange-400"
                               style="text-decoration: none !important;">
                                {{ number_format((float)$event->gps_lat, 5) }}, {{ number_format((float)$event->gps_lon, 5) }}
                            </a>
                        </dd>
                    </div>
                    @endif

                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-gray-500 dark:text-gray-400">{{ __('sport-event.public.label_type') }}</dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">{{ __('sport-event.type_enum.' . $event->event_type->value) }}</dd>
                    </div>

                    @if($event->sportDiscipline)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-gray-500 dark:text-gray-400">{{ __('sport-event.public.label_discipline') }}</dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">{{ $event->sportDiscipline->short_name }}</dd>
                    </div>
                    @endif

                    @if($event->sportLevel)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-gray-500 dark:text-gray-400">{{ __('sport-event.public.label_level') }}</dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">{{ $event->sportLevel->short_name }}</dd>
                    </div>
                    @endif

                    @if(count($event->organization ?? []) > 0)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-gray-500 dark:text-gray-400">{{ __('sport-event.public.label_organizer') }}</dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">{{ Arr::join($event->organization, ', ') }}</dd>
                    </div>
                    @endif

                    @if(count($event->region ?? []) > 0)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-gray-500 dark:text-gray-400">{{ __('sport-event.public.label_region') }}</dt>
                        <dd class="text-right font-medium text-gray-900 dark:text-gray-100">{{ Arr::join($event->region, ', ') }}</dd>
                    </div>
                    @endif

                    @if($event->ranking_coefficient)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-gray-500 dark:text-gray-400">{{ __('sport-event.public.label_ranking') }}</dt>
                        <dd class="text-right font-medium tabular-nums text-gray-900 dark:text-gray-100">{{ $event->ranking_coefficient }}</dd>
                    </div>
                    @endif

                    @if($event->oris_id)
                    <div class="flex items-start justify-between gap-3 border-t border-gray-100 pt-3 dark:border-gray-800">
                        <dt class="shrink-0 text-gray-500 dark:text-gray-400">ORIS</dt>
                        <dd class="text-right">
                            <a href="{{ OrisApiService::ORIS_URL }}/Zavod?id={{ $event->oris_id }}"
                               target="_blank"
                               class="inline-flex items-center gap-1 font-medium text-orange-600 transition-colors hover:text-orange-700 dark:text-orange-400 dark:hover:text-orange-300"
                               style="text-decoration: none !important;">
                                {{ $event->oris_id }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </a>
                        </dd>
                    </div>
                    @endif

                </dl>
            </section>

            {{-- Sdílená doprava --}}
            @if($hasTransport)
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm md:p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-50">{{ __('sport-event.public.transport_title') }}</h2>
                <p class="mt-1.5 text-sm text-gray-500 dark:text-gray-400">{{ __('sport-event.transport_type_enum.' . $event->transport_type->value) }}</p>

                @if($transportOffersCount > 0)
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-lg border border-gray-100 p-3 text-center dark:border-gray-800">
                        <div class="text-xl font-bold text-gray-900 dark:text-gray-50">{{ $transportOffersCount }}</div>
                        <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ trans_choice('sport-event.public.transport_offers', $transportOffersCount) }}</div>
                    </div>
                    <div class="rounded-lg border border-gray-100 p-3 text-center dark:border-gray-800">
                        <div class="text-xl font-bold {{ $transportFreeSeats > 0 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-900 dark:text-gray-50' }}">{{ $transportFreeSeats }}</div>
                        <div class="mt-0.5 text-xs text-gray-500 dark:text-gray-400">{{ trans_choice('sport-event.public.transport_free_seats', $transportFreeSeats) }}</div>
                    </div>
                </div>
                @else
                <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">{{ __('sport-event.public.transport_no_offers') }}</p>
                @endif

                <p class="mt-4 text-xs text-gray-400 dark:text-gray-500">{{ __('sport-event.public.transport_login_hint') }}</p>
            </section>
            @endif

            {{-- Dokumenty & odkazy --}}
            @if($hasLinks)
            <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <header class="flex items-baseline justify-between p-5 pb-3 md:p-6 md:pb-3">
                    <h2 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-50">{{ __('sport-event.public.links_title') }}</h2>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $event->sportEventLinks->count() }}</span>
                </header>
                <ul class="px-2 pb-2">
                    @foreach($event->sportEventLinks as $link)
                        <li>
                            <a href="{{ $link->source_url }}"
                               target="_blank"
                               class="flex items-center gap-3 rounded-lg px-3 py-2.5 transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/60"
                               style="text-decoration: none !important;">
                                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-md bg-orange-500/10 text-orange-600 dark:text-orange-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                    </svg>
                                </span>
                                <span class="min-w-0 flex-1 truncate text-sm font-medium text-gray-800 dark:text-gray-200">
                                    {{ __('sport-event.type_enum_links.' . $link->source_type->value) }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
            @endif

            {{-- Počasí --}}
            @if($hasWeather)
            @php $weatherId = isset($event->weather['weather'][0]['id']) ? (int) $event->weather['weather'][0]['id'] : null; @endphp
            <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm md:p-6 dark:border-gray-800 dark:bg-gray-900">
                <h2 class="text-lg font-semibold leading-none tracking-tight text-gray-900 dark:text-gray-50">{{ __('sport-event.public.weather_title') }}</h2>
                <div class="mt-4 flex items-center gap-4">
                    <x-weather-icon :weather-id="$weatherId" class="h-12 w-12 shrink-0 text-orange-500" />
                    <div>
                        <div class="text-3xl font-bold tabular-nums text-gray-900 dark:text-gray-50">
                            {{ isset($event->weather['main']['temp']) ? round($event->weather['main']['temp'], 1) : '' }}&deg;C
                        </div>
                        <div class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                            {{ $event->weather['weather'][0]['description'] ?? '' }}
                        </div>
                    </div>
                </div>
            </section>
            @endif

        </div>

    </div>

</div>

@endsection
