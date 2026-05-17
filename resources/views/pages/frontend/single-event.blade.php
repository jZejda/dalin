@php
    use App\Models\SportEvent;
    use App\Services\OrisApiService;
    use App\Shared\Helpers\AppHelper;
    use Carbon\Carbon;

    /** @var SportEvent $event */

    $hasMap       = $event->gps_lat && $event->gps_lon;
    $hasCategories = $event->sportClasses->count() > 0;
    $hasNews      = $event->sportEventNews->count() > 0;
    $hasLinks     = $event->sportEventLinks->count() > 0;
    $hasServices  = $event->sportServices->count() > 0;
    $hasWeather   = !is_null($event->weather);
    $sectionNum   = 0;
@endphp

@extends('layouts.app')

@section('title', $event->name)
@section('sponsorSectionId', $sponsorSectionId)

@section('content')

{{-- ═══════════════════════════════════════════════════════════
     HERO — světlá hlavička s breadcrumb, názvem a metadaty
     ═══════════════════════════════════════════════════════════ --}}
<div class="bg-white dark:bg-gray-900 pt-4">
    <div class="container mx-auto px-4">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-3">
            <a href="/" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Domů</a>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
            <a href="/" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Závody</a>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
            <span class="font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $event->name }}</span>
        </nav>

        {{-- Cancelled inline badge --}}
        @if($event->cancelled)
            <div class="mb-3 inline-flex items-center gap-2 px-3 py-1 bg-red-600 text-white text-xs font-bold rounded uppercase tracking-wide">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
                Zrušeno
            </div>
        @endif

        {{-- Title --}}
        <h1 class="text-2xl md:text-4xl font-extrabold text-gray-800 dark:text-gray-100 leading-tight tracking-tight">
            {{ $event->name }}
        </h1>
        @if($event->alt_name)
            <p class="mt-1.5 text-base text-gray-500 dark:text-gray-400">{{ $event->alt_name }}</p>
        @endif

        {{-- Metadata row --}}
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 mt-3 pb-4 text-sm text-gray-600 dark:text-gray-400">

            {{-- Date --}}
            <span class="flex items-center gap-1.5 font-mono text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
                {{ $event->date->format(AppHelper::DATE_FORMAT) }}
                @if($event->date_end && $event->date_end->gt($event->date))
                    — {{ $event->date_end->format(AppHelper::DATE_FORMAT) }}
                @endif
            </span>

            @if($event->place)
                <span class="flex items-center gap-1.5 font-mono text-xs">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0 1 15 0Z" />
                    </svg>
                    {{ $event->place }}
                </span>
            @endif

            {{-- Tags --}}
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="inline-flex items-center py-0.5 px-2 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                    {{ __('sport-event.type_enum.' . $event->event_type->value) }}
                </span>
                @if($event->sportDiscipline)
                    <span class="inline-flex items-center py-0.5 px-2 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        {{ $event->sportDiscipline->short_name }}
                    </span>
                @endif
                @if($event->sportLevel)
                    <span class="inline-flex items-center py-0.5 px-2 rounded text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        {{ $event->sportLevel->short_name }}
                    </span>
                @endif
                @if($event->oris_id)
                    <a href="{{ OrisApiService::ORIS_URL }}/Zavod?id={{ $event->oris_id }}"
                       target="_blank"
                       class="inline-flex items-center gap-1 py-0.5 px-2 rounded text-xs font-medium bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300 hover:bg-amber-200 dark:hover:bg-amber-900/70 transition-colors"
                       style="text-decoration: none !important;">
                        ORIS {{ $event->oris_id }}
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                        </svg>
                    </a>
                @endif
            </div>
        </div>

    </div>
    <div class="border-b border-gray-200 dark:border-gray-700"></div>
</div>
<div class="h-0.5 bg-yellow-400 dark:bg-yellow-500"></div>

{{-- ═══════════════════════════════════════════════════════════
     OBSAH
     ═══════════════════════════════════════════════════════════ --}}
<div class="container mx-auto px-4 py-6 space-y-5">

    {{-- Cancelled detailed alert --}}
    @if($event->cancelled)
        <div class="border-l-4 border-red-500 bg-red-50 dark:bg-red-900/20 p-4 rounded-r-lg flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <div>
                <div class="font-bold text-red-700 dark:text-red-400">Závod byl zrušen</div>
                @if($event->cancelled_reason)
                    <div class="mt-0.5 text-sm text-red-600 dark:text-red-300">{{ $event->cancelled_reason }}</div>
                @endif
            </div>
        </div>
    @endif

    {{-- Warning --}}
    @if($event->event_warning)
        <div class="border-l-4 border-amber-400 bg-amber-50 dark:bg-amber-900/30 p-4 rounded-r-lg flex items-start gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
            <div class="text-sm text-amber-800 dark:text-amber-200">{!! $event->event_warning !!}</div>
        </div>
    @endif

    {{-- ─────────────────────────────────────────────
         § 01  MAPA ZÁVODU
         ───────────────────────────────────────────── --}}
    @if($hasMap)
    @php $sectionNum++ @endphp
    <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="px-5 pt-5 pb-3 flex items-baseline justify-between">
            <div>
                <div class="text-xs font-mono uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">§ {{ str_pad($sectionNum, 2, '0', STR_PAD_LEFT) }}</div>
                <h2 class="mt-0.5 text-xl font-semibold text-gray-900 dark:text-white tracking-tight">Mapa závodu</h2>
            </div>
            @if($event->gps_lat && $event->gps_lon)
                <a href="https://www.google.com/maps?q={{ $event->gps_lat }},{{ $event->gps_lon }}"
                   target="_blank"
                   class="flex items-center gap-1.5 text-xs font-mono text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
                   style="text-decoration: none !important;">
                    {{ number_format((float)$event->gps_lat, 4) }}, {{ number_format((float)$event->gps_lon, 4) }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                </a>
            @endif
        </div>
        @livewire(\App\Livewire\Shared\Maps\LeafletMap::class, [
            'sportEvent' => $event,
            'publicMap'  => true,
        ])
    </section>
    @endif

    {{-- ─────────────────────────────────────────────
         § 02 + § 03  Základní informace + Kategorie
         ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        {{-- § 02 Základní informace --}}
        @php $sectionNum++ @endphp
        <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5 {{ $hasCategories ? 'lg:col-span-5' : 'lg:col-span-12' }}">
            <div class="text-xs font-mono uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">§ {{ str_pad($sectionNum, 2, '0', STR_PAD_LEFT) }}</div>
            <h2 class="mt-0.5 mb-5 text-xl font-semibold text-gray-900 dark:text-white tracking-tight">Základní informace</h2>

            <dl class="grid grid-cols-[110px_1fr] gap-x-4 gap-y-3.5 text-sm">

                <dt class="font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 pt-0.5">Datum</dt>
                <dd class="text-gray-800 dark:text-gray-200">
                    {{ $event->date->format(AppHelper::DATE_FORMAT) }}
                    @if($event->date_end && $event->date_end->gt($event->date))
                        — {{ $event->date_end->format(AppHelper::DATE_FORMAT) }}
                    @endif
                </dd>

                @if($event->start_time)
                    <dt class="font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 pt-0.5">Start</dt>
                    <dd class="font-mono text-sm text-gray-800 dark:text-gray-200">{{ $event->start_time }}</dd>
                @endif

                @if($event->place)
                    <dt class="font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 pt-0.5">Místo</dt>
                    <dd class="text-gray-800 dark:text-gray-200">{{ $event->place }}</dd>
                @endif

                @if($event->gps_lat && $event->gps_lon)
                    <dt class="font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 pt-0.5">GPS</dt>
                    <dd>
                        <a href="https://www.google.com/maps?q={{ $event->gps_lat }},{{ $event->gps_lon }}"
                           target="_blank"
                           class="font-mono text-xs text-gray-600 dark:text-gray-300 hover:text-amber-600 dark:hover:text-amber-400 transition-colors"
                           style="text-decoration: none !important;">
                            {{ number_format((float)$event->gps_lat, 5) }}, {{ number_format((float)$event->gps_lon, 5) }}
                        </a>
                    </dd>
                @endif

                <dt class="font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 pt-0.5">Druh</dt>
                <dd class="text-gray-800 dark:text-gray-200 text-xs leading-relaxed">{{ __('sport-event.type_enum.' . $event->event_type->value) }}</dd>

                @if($event->sportDiscipline)
                    <dt class="font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 pt-0.5">Disciplína</dt>
                    <dd class="text-gray-800 dark:text-gray-200">{{ $event->sportDiscipline->short_name }}</dd>
                @endif

                @if($event->sportLevel)
                    <dt class="font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 pt-0.5">Úroveň</dt>
                    <dd class="text-gray-800 dark:text-gray-200">{{ $event->sportLevel->short_name }}</dd>
                @endif

                @if(count($event->organization ?? []) > 0)
                    <dt class="font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 pt-0.5">Pořadatel</dt>
                    <dd class="text-gray-800 dark:text-gray-200 text-xs leading-relaxed">{{ Arr::join($event->organization, ', ') }}</dd>
                @endif

                @if(count($event->region ?? []) > 0)
                    <dt class="font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 pt-0.5">Region</dt>
                    <dd class="text-gray-800 dark:text-gray-200">{{ Arr::join($event->region, ', ') }}</dd>
                @endif

                @if($event->oris_id)
                    <dt class="font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 pt-0.5">ORIS</dt>
                    <dd>
                        <a href="{{ OrisApiService::ORIS_URL }}/Zavod?id={{ $event->oris_id }}"
                           target="_blank"
                           class="inline-flex items-center gap-1 font-mono text-xs text-amber-700 dark:text-amber-400 hover:underline"
                           style="text-decoration: none !important;">
                            {{ $event->oris_id }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                    </dd>
                @endif

                {{-- Entry deadlines --}}
                @if($event->entry_date_1)
                    <dt class="font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 pt-0.5 col-span-2 border-t border-gray-100 dark:border-gray-700 pt-3.5 mt-1">Přihlášky</dt>
                    @if($event->entry_date_1)
                        <dt class="font-mono text-xs text-gray-400 dark:text-gray-500 pt-0.5 pl-2">1. termín</dt>
                        <dd class="font-mono text-xs {{ Carbon::parse($event->entry_date_1)->isPast() ? 'line-through text-gray-400' : 'text-gray-800 dark:text-gray-200' }}">
                            {{ Carbon::parse($event->entry_date_1)->format(AppHelper::DATE_TIME_FORMAT) }}
                        </dd>
                    @endif
                    @if($event->entry_date_2)
                        <dt class="font-mono text-xs text-gray-400 dark:text-gray-500 pt-0.5 pl-2">2. termín</dt>
                        <dd class="font-mono text-xs {{ Carbon::parse($event->entry_date_2)->isPast() ? 'line-through text-gray-400' : 'text-gray-800 dark:text-gray-200' }}">
                            {{ Carbon::parse($event->entry_date_2)->format(AppHelper::DATE_TIME_FORMAT) }}
                            @if($event->increase_entry_fee_2)
                                <span class="ml-1 text-amber-600 dark:text-amber-400">+{{ $event->increase_entry_fee_2 }} Kč</span>
                            @endif
                        </dd>
                    @endif
                    @if($event->entry_date_3)
                        <dt class="font-mono text-xs text-gray-400 dark:text-gray-500 pt-0.5 pl-2">3. termín</dt>
                        <dd class="font-mono text-xs {{ Carbon::parse($event->entry_date_3)->isPast() ? 'line-through text-gray-400' : 'text-gray-800 dark:text-gray-200' }}">
                            {{ Carbon::parse($event->entry_date_3)->format(AppHelper::DATE_TIME_FORMAT) }}
                            @if($event->increase_entry_fee_3)
                                <span class="ml-1 text-amber-600 dark:text-amber-400">+{{ $event->increase_entry_fee_3 }} Kč</span>
                            @endif
                        </dd>
                    @endif
                @endif

                {{-- Ranking coefficient --}}
                @if($event->ranking_coefficient)
                    <dt class="font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 pt-0.5">Ranking</dt>
                    <dd class="font-mono text-sm text-gray-800 dark:text-gray-200">{{ $event->ranking_coefficient }}</dd>
                @endif

            </dl>
        </section>

        {{-- § 03 Kategorie --}}
        @if($hasCategories)
        @php $sectionNum++ @endphp
        <section class="lg:col-span-7 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5">
            <div class="flex items-baseline justify-between mb-5">
                <div>
                    <div class="text-xs font-mono uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">§ {{ str_pad($sectionNum, 2, '0', STR_PAD_LEFT) }}</div>
                    <h2 class="mt-0.5 text-xl font-semibold text-gray-900 dark:text-white tracking-tight">Kategorie</h2>
                </div>
                <span class="font-mono text-xs text-gray-400 dark:text-gray-500">{{ $event->sportClasses->count() }} kat.</span>
            </div>
            <div class="flex flex-wrap gap-2">
                @foreach($event->sportClasses->sortBy('name') as $class)
                    <span class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-700 rounded font-mono text-sm font-semibold text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50">
                        <span class="w-1.5 h-1.5 bg-amber-400 rounded-sm shrink-0"></span>
                        {{ $class->name }}
                    </span>
                @endforeach
            </div>
        </section>
        @endif

    </div>{{-- end § 02 + § 03 grid --}}

    {{-- ─────────────────────────────────────────────
         § 04 + § 05  Novinky + Dokumenty & Odkazy
         ───────────────────────────────────────────── --}}
    @if($hasNews || $hasLinks)
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

        {{-- § 04 Rychlé novinky --}}
        @if($hasNews)
        @php $sectionNum++ @endphp
        <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden {{ $hasLinks ? 'lg:col-span-7' : 'lg:col-span-12' }}">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-baseline justify-between">
                <div>
                    <div class="text-xs font-mono uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">§ {{ str_pad($sectionNum, 2, '0', STR_PAD_LEFT) }}</div>
                    <h2 class="mt-0.5 text-xl font-semibold text-gray-900 dark:text-white tracking-tight">Rychlé novinky</h2>
                </div>
                <span class="font-mono text-xs text-gray-400 dark:text-gray-500">{{ $event->sportEventNews->count() }}</span>
            </header>
            <ol>
                @foreach($event->sportEventNews->sortByDesc('date') as $i => $news)
                    <li class="grid grid-cols-[88px_1fr] md:grid-cols-[110px_1fr] gap-4 px-5 py-4 {{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-700/50' : '' }}">
                        <div class="font-mono text-xs pt-0.5 {{ $i === 0 ? 'text-amber-500 dark:text-amber-400' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ Carbon::parse($news->date)->format(AppHelper::DATE_FORMAT) }}
                        </div>
                        <div class="text-sm text-gray-800 dark:text-gray-200 leading-relaxed">{!! $news->text !!}</div>
                    </li>
                @endforeach
            </ol>
        </section>
        @endif

        {{-- § 05 Dokumenty & Odkazy --}}
        @if($hasLinks)
        @php $sectionNum++ @endphp
        <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden {{ $hasNews ? 'lg:col-span-5' : 'lg:col-span-12' }}">
            <header class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-baseline justify-between">
                <div>
                    <div class="text-xs font-mono uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">§ {{ str_pad($sectionNum, 2, '0', STR_PAD_LEFT) }}</div>
                    <h2 class="mt-0.5 text-xl font-semibold text-gray-900 dark:text-white tracking-tight">Dokumenty & Odkazy</h2>
                </div>
                <span class="font-mono text-xs text-gray-400 dark:text-gray-500">{{ $event->sportEventLinks->count() }}</span>
            </header>
            <ul>
                @foreach($event->sportEventLinks as $i => $link)
                    <li class="{{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-700/50' : '' }}">
                        <a href="{{ $link->source_url }}"
                           target="_blank"
                           class="flex items-center gap-3 px-5 py-3.5 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition-colors"
                           style="text-decoration: none !important;">
                            <span class="w-9 h-9 flex items-center justify-center rounded bg-amber-100 dark:bg-amber-900/40 font-mono text-xs font-bold text-amber-700 dark:text-amber-400 shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                </svg>
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="block text-sm font-medium text-gray-800 dark:text-gray-200 truncate">
                                    {{ __('sport-event.type_enum_links.' . $link->source_type->value) }}
                                </span>
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
        @endif

    </div>{{-- end § 04 + § 05 grid --}}
    @endif

    {{-- ─────────────────────────────────────────────
         Volitelné sekce — zobrazí se pokud existují data
         ───────────────────────────────────────────── --}}

    {{-- Event info (prose) --}}
    @if($event->event_info)
    @php $sectionNum++ @endphp
    <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5">
        <div class="text-xs font-mono uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">§ {{ str_pad($sectionNum, 2, '0', STR_PAD_LEFT) }}</div>
        <h2 class="mt-0.5 mb-4 text-xl font-semibold text-gray-900 dark:text-white tracking-tight">Informace o akci</h2>
        <div class="prose dark:prose-invert max-w-none text-sm">{!! $event->event_info !!}</div>
    </section>
    @endif

    {{-- Entry description --}}
    @if($event->entry_desc)
    @php $sectionNum++ @endphp
    <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5">
        <div class="text-xs font-mono uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">§ {{ str_pad($sectionNum, 2, '0', STR_PAD_LEFT) }}</div>
        <h2 class="mt-0.5 mb-4 text-xl font-semibold text-gray-900 dark:text-white tracking-tight">Přihlašování</h2>
        <div class="prose dark:prose-invert max-w-none text-sm">{!! $event->entry_desc !!}</div>
    </section>
    @endif

    {{-- Services --}}
    @if($hasServices)
    @php $sectionNum++ @endphp
    <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
        <div class="px-5 pt-5 pb-3">
            <div class="text-xs font-mono uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500">§ {{ str_pad($sectionNum, 2, '0', STR_PAD_LEFT) }}</div>
            <h2 class="mt-0.5 text-xl font-semibold text-gray-900 dark:text-white tracking-tight">Služby</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="px-5 py-2.5 text-left font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 font-medium">Název</th>
                        <th class="px-5 py-2.5 text-left font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 font-medium">Cena</th>
                        <th class="px-5 py-2.5 text-left font-mono text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 font-medium">Dostupné</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($event->sportServices as $service)
                        <tr class="{{ !$loop->last ? 'border-b border-gray-50 dark:border-gray-700/50' : '' }} hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-5 py-3 font-medium text-gray-800 dark:text-gray-200">{{ $service->service_name_cz }}</td>
                            <td class="px-5 py-3 font-mono text-sm text-gray-600 dark:text-gray-400">{{ $service->unit_price }} Kč</td>
                            <td class="px-5 py-3 font-mono text-sm text-gray-600 dark:text-gray-400">{{ $service->qty_remaining ?? $service->qty_available ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    @endif

    {{-- Weather --}}
    @if($hasWeather)
    @php
        $sectionNum++;
        $iconString = $event->weather['weather'][0]['icon'] ?? '';
    @endphp
    <section class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg p-5">
        <div class="text-xs font-mono uppercase tracking-[0.14em] text-gray-400 dark:text-gray-500 mb-0.5">§ {{ str_pad($sectionNum, 2, '0', STR_PAD_LEFT) }}</div>
        <h2 class="text-xl font-semibold text-gray-900 dark:text-white tracking-tight mb-4">Počasí</h2>
        <div class="flex items-center gap-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 text-amber-400 shrink-0">
                @if($iconString === '01d' || $iconString === '01n')
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M14.828 14.828a4 4 0 1 0 -5.656 -5.656a4 4 0 0 0 5.656 5.656z"></path>
                    <path d="M6.343 17.657l-1.414 1.414m0-16.97l1.414 1.414M17.657 6.343l1.414 -1.414M17.657 17.657l1.414 1.414M4 12h-2m18 0h-2M12 4v-2m0 18v-2"></path>
                @elseif(in_array($iconString, ['02d','03d','04d']))
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M6.657 18c-2.572 0 -4.657 -2.007 -4.657 -4.483c0 -2.475 2.085 -4.482 4.657 -4.482c.393 -1.762 1.794 -3.2 3.675 -3.773c1.88 -.572 3.956 -.193 5.444 1c1.488 1.19 2.162 3.007 1.77 4.769h.99c1.913 0 3.464 1.56 3.464 3.486c0 1.927 -1.551 3.487 -3.465 3.487h-11.878"></path>
                @elseif(in_array($iconString, ['09d','10d','10n']))
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7"></path>
                    <path d="M11 13v2m0 3v2m4 -5v2m0 3v2"></path>
                @elseif($iconString === '11d')
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1"></path>
                    <path d="M13 14l-2 4l3 0l-2 4"></path>
                @elseif($iconString === '13d')
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7"></path>
                    <path d="M11 15v.01m0 3v.01m0 3v.01m4 -4v.01m0 3v.01"></path>
                @else
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                    <path d="M6.657 18c-2.572 0 -4.657 -2.007 -4.657 -4.483c0 -2.475 2.085 -4.482 4.657 -4.482c.393 -1.762 1.794 -3.2 3.675 -3.773c1.88 -.572 3.956 -.193 5.444 1c1.488 1.19 2.162 3.007 1.77 4.769h.99c1.913 0 3.464 1.56 3.464 3.486c0 1.927 -1.551 3.487 -3.465 3.487h-11.878"></path>
                @endif
            </svg>
            <div>
                <div class="text-3xl font-bold font-mono text-gray-900 dark:text-white">
                    {{ isset($event->weather['main']['temp']) ? round($event->weather['main']['temp'], 1) : '' }}&deg;C
                </div>
                <div class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $event->weather['weather'][0]['description'] ?? '' }}
                </div>
            </div>
        </div>
    </section>
    @endif

</div>

@endsection
