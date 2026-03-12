@php
    use App\Models\SportEvent;
    use App\Services\OrisApiService;
    use Carbon\Carbon;

    /** @var SportEvent $event */
@endphp

@extends('layouts.app')

@section('title', $event->name)
@section('sponsorSectionId', $sponsorSectionId)

@section('content')
    <div class="bg-white dark:bg-gray-900 app-front-content">

        {{-- 1. Hero header --}}
        <div class="py-4 md:py-8 bg-[url(https://abmbrno.cz/images/topography1.svg)] bg-slate-950 text-gray-700 dark:text-gray-300">
            <div class="container mx-auto px-4">
                <div class="text-2xl md:text-4xl bg-gradient-to-r from-yellow-400 to-amber-200 inline-block text-transparent bg-clip-text font-extrabold">
                    {{ $event->name }}
                </div>
                @if($event->alt_name)
                    <div class="text-lg text-gray-400 mt-1">{{ $event->alt_name }}</div>
                @endif

                {{-- Cancelled banner --}}
                @if($event->cancelled)
                    <div class="mt-4 bg-red-900/80 border border-red-500 text-red-100 rounded-lg px-4 py-3">
                        <span class="font-bold text-lg">ZRUŠENO</span>
                        @if($event->cancelled_reason)
                            <span class="ml-2">— {{ $event->cancelled_reason }}</span>
                        @endif
                    </div>
                @endif

                {{-- Badge row --}}
                <div class="flex flex-wrap items-center gap-2 mt-4">
                    {{-- Date --}}
                    <div class="inline-flex items-center gap-x-1.5 py-1 px-2 rounded-md text-xs font-medium border border-gray-600 bg-gray-800 text-gray-200 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                        {{ $event->date->format(\App\Shared\Helpers\AppHelper::DATE_FORMAT) }}
                        @if($event->date_end && $event->date_end->gt($event->date))
                            — {{ $event->date_end->format(\App\Shared\Helpers\AppHelper::DATE_FORMAT) }}
                        @endif
                    </div>

                    {{-- Event type --}}
                    <div class="inline-flex items-center py-1 px-2 rounded-md text-xs font-medium border border-gray-600 bg-gray-800 text-gray-200 shadow-sm">
                        {{ __('sport-event.type_enum.' . $event->event_type->value) }}
                    </div>

                    {{-- Discipline --}}
                    @if($event->sportDiscipline)
                        <div class="inline-flex items-center py-1 px-2 rounded-md text-xs font-medium border border-gray-600 bg-gray-800 text-gray-200 shadow-sm">
                            {{ $event->sportDiscipline->short_name }}
                        </div>
                    @endif

                    {{-- Level --}}
                    @if($event->sportLevel)
                        <div class="inline-flex items-center py-1 px-2 rounded-md text-xs font-medium border border-gray-600 bg-gray-800 text-gray-200 shadow-sm">
                            {{ $event->sportLevel->short_name }}
                        </div>
                    @endif

                    {{-- Organization --}}
                    @if(count($event->organization ?? []) > 0)
                        <div class="inline-flex items-center py-1 px-2 rounded-md text-xs font-medium border border-gray-600 bg-gray-800 text-gray-200 shadow-sm">
                            {{ Arr::join($event->organization, ', ') }}
                        </div>
                    @endif

                    {{-- Region --}}
                    @if(count($event->region ?? []) > 0)
                        <div class="inline-flex items-center py-1 px-2 rounded-md text-xs font-medium border border-gray-600 bg-gray-800 text-gray-200 shadow-sm">
                            {{ Arr::join($event->region, ', ') }}
                        </div>
                    @endif

                    {{-- ORIS link --}}
                    @if($event->oris_id)
                        <a href="{{ OrisApiService::ORIS_URL }}/Zavod?id={{ $event->oris_id }}"
                           target="_blank"
                           class="inline-flex items-center gap-x-1 py-1 px-2 rounded-md text-xs font-medium border border-yellow-600 bg-yellow-700 text-yellow-100 shadow-sm no-underline hover:bg-yellow-600"
                           style="text-decoration: none !important;">
                            ORIS {{ $event->oris_id }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                            </svg>
                        </a>
                    @endif
                </div>

                {{-- Place and start time --}}
                <div class="flex flex-wrap items-center gap-4 mt-3 text-gray-400 text-sm">
                    @if($event->place)
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0 1 15 0Z" />
                            </svg>
                            {{ $event->place }}
                        </div>
                    @endif
                    @if($event->start_time)
                        <div class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            Start: {{ $event->start_time }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="container mx-auto px-4 py-6 space-y-6">

            {{-- 2. Warning --}}
            @if($event->event_warning)
                <div class="border-l-4 border-amber-400 bg-amber-50 dark:bg-amber-900/30 p-4 rounded-r-lg">
                    <div class="flex items-start gap-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <div class="text-amber-800 dark:text-amber-200">{!! $event->event_warning !!}</div>
                    </div>
                </div>
            @endif

            {{-- 3. Map --}}
            @if($event->gps_lat && $event->gps_lon)
                <section>
                    @livewire(\App\Livewire\Shared\Maps\LeafletMap::class, [
                        'sportEvent' => $event,
                        'publicMap' => true,
                    ])
                </section>

                <div class="relative flex items-center">
                    <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                </div>
            @endif

            {{-- 4. Event info --}}
            @if($event->event_info)
                <section>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Informace o akci</h2>
                    <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200">
                        {!! $event->event_info !!}
                    </div>
                </section>

                <div class="relative flex items-center">
                    <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                </div>
            @endif

            {{-- 5. Entry description --}}
            @if($event->entry_desc)
                <section>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Popis přihlášek</h2>
                    <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200">
                        {!! $event->entry_desc !!}
                    </div>
                </section>

                <div class="relative flex items-center">
                    <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                </div>
            @endif

            {{-- 6. Entry deadlines --}}
            @if($event->entry_date_1)
                <section>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Termíny přihlášek</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3">Termín</th>
                                    <th class="px-4 py-3">Datum</th>
                                    <th class="px-4 py-3">Navýšení</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($event->entry_date_1)
                                    <tr class="border-b dark:border-gray-600 {{ Carbon::parse($event->entry_date_1)->isPast() ? 'opacity-50' : '' }}">
                                        <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">1. termín</td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-white">{{ Carbon::parse($event->entry_date_1)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT) }}</td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-white">—</td>
                                    </tr>
                                @endif
                                @if($event->entry_date_2)
                                    <tr class="border-b dark:border-gray-600 {{ Carbon::parse($event->entry_date_2)->isPast() ? 'opacity-50' : '' }}">
                                        <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">2. termín</td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-white">{{ Carbon::parse($event->entry_date_2)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT) }}</td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-white">
                                            @if($event->increase_entry_fee_2)
                                                +{{ $event->increase_entry_fee_2 }} Kč
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if($event->entry_date_3)
                                    <tr class="border-b dark:border-gray-600 {{ Carbon::parse($event->entry_date_3)->isPast() ? 'opacity-50' : '' }}">
                                        <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">3. termín</td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-white">{{ Carbon::parse($event->entry_date_3)->format(\App\Shared\Helpers\AppHelper::DATE_TIME_FORMAT) }}</td>
                                        <td class="px-4 py-2 text-gray-900 dark:text-white">
                                            @if($event->increase_entry_fee_3)
                                                +{{ $event->increase_entry_fee_3 }} Kč
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </section>

                <div class="relative flex items-center">
                    <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                </div>
            @endif

            {{-- 7. Classes --}}
            @if($event->sportClasses->count() > 0)
                <section>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Kategorie</h2>
                    <div class="relative overflow-hidden bg-white shadow-lg dark:bg-gray-800 sm:rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-4 py-3">Název</th>
                                        <th class="px-4 py-3">Vzdálenost</th>
                                        <th class="px-4 py-3">Převýšení</th>
                                        <th class="px-4 py-3">Kontrol</th>
                                        <th class="px-4 py-3">Startovné</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($event->sportClasses->sortBy('name') as $class)
                                        <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $class->name }}</td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-white">
                                                @if($class->distance)
                                                    {{ number_format((float)$class->distance / 1000, 2, '.', '') }} km
                                                @else
                                                    —
                                                @endif
                                            </td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-white">
                                                {{ $class->climbing ? $class->climbing . ' m' : '—' }}
                                            </td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-white">
                                                {{ $class->controls ?? '—' }}
                                            </td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-white">
                                                {{ $class->fee ? $class->fee . ' Kč' : '—' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <div class="relative flex items-center">
                    <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                </div>
            @endif

            {{-- 8. Links and documents --}}
            @if($event->sportEventLinks->count() > 0)
                <section>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Odkazy a dokumenty</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                        @foreach($event->sportEventLinks as $link)
                            <a href="{{ $link->source_url }}"
                               target="_blank"
                               class="flex items-center gap-2 p-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition no-underline shadow-sm"
                               style="text-decoration: none !important;">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" />
                                </svg>
                                <span class="text-sm font-medium">
                                    {{ __('sport-event.type_enum_links.' . $link->source_type->value) }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 ml-auto text-gray-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </section>

                <div class="relative flex items-center">
                    <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                </div>
            @endif

            {{-- 9. Services --}}
            @if($event->sportServices->count() > 0)
                <section>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Služby</h2>
                    <div class="relative overflow-hidden bg-white shadow-lg dark:bg-gray-800 sm:rounded-lg">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th class="px-4 py-3">Název</th>
                                        <th class="px-4 py-3">Cena</th>
                                        <th class="px-4 py-3">Dostupné</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($event->sportServices as $service)
                                        <tr class="border-b dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            <td class="px-4 py-2 font-medium text-gray-900 dark:text-white">{{ $service->service_name_cz }}</td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $service->unit_price }} Kč</td>
                                            <td class="px-4 py-2 text-gray-900 dark:text-white">{{ $service->qty_remaining ?? $service->qty_available ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

                <div class="relative flex items-center">
                    <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                </div>
            @endif

            {{-- 10. News --}}
            @if($event->sportEventNews->count() > 0)
                <section>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Novinky</h2>
                    <div class="space-y-3">
                        @foreach($event->sportEventNews->sortByDesc('date') as $news)
                            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                    {{ Carbon::parse($news->date)->format(\App\Shared\Helpers\AppHelper::DATE_FORMAT) }}
                                </div>
                                <div class="text-gray-800 dark:text-gray-200">
                                    {!! $news->text !!}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="relative flex items-center">
                    <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                </div>
            @endif

            {{-- 11. Weather --}}
            @if(!is_null($event->weather))
                @php
                    $iconString = isset($event->weather['weather'][0]['icon']) ? $event->weather['weather'][0]['icon'] : '';
                @endphp
                <section>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Počasí</h2>
                    <div class="flex items-center gap-4 p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" class="h-12 w-12 text-gray-700 dark:text-gray-300">
                            @if($iconString === '01d' || $iconString === '01n')
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M14.828 14.828a4 4 0 1 0 -5.656 -5.656a4 4 0 0 0 5.656 5.656z"></path>
                                <path d="M6.343 17.657l-1.414 1.414"></path>
                                <path d="M6.343 6.343l-1.414 -1.414"></path>
                                <path d="M17.657 6.343l1.414 -1.414"></path>
                                <path d="M17.657 17.657l1.414 1.414"></path>
                                <path d="M4 12h-2"></path>
                                <path d="M12 4v-2"></path>
                                <path d="M20 12h2"></path>
                                <path d="M12 20v2"></path>
                            @elseif($iconString == '02d' || $iconString == '03d' || $iconString == '04d')
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M6.657 18c-2.572 0 -4.657 -2.007 -4.657 -4.483c0 -2.475 2.085 -4.482 4.657 -4.482c.393 -1.762 1.794 -3.2 3.675 -3.773c1.88 -.572 3.956 -.193 5.444 1c1.488 1.19 2.162 3.007 1.77 4.769h.99c1.913 0 3.464 1.56 3.464 3.486c0 1.927 -1.551 3.487 -3.465 3.487h-11.878"></path>
                            @elseif($iconString == '10n')
                                <line x1="8" y1="13" x2="8" y2="21"/>
                                <line x1="16" y1="13" x2="16" y2="21"/>
                                <line x1="12" y1="15" x2="12" y2="23"/>
                                <path d="M20 16.58A5 5 0 0 0 18 7h-1.26A8 8 0 1 0 4 15.25"/>
                            @elseif($iconString === '09d' || $iconString === '10d')
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
                            @elseif($iconString === '50d')
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                <path d="M7 16a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-12"></path>
                                <path d="M5 20l14 0"></path>
                            @endif
                        </svg>
                        <div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ isset($event->weather['main']['temp']) ? round($event->weather['main']['temp'], 1) : '' }}&deg;C
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                {{ isset($event->weather['weather'][0]['description']) ? $event->weather['weather'][0]['description'] : '' }}
                            </div>
                        </div>
                    </div>
                </section>

                <div class="relative flex items-center">
                    <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                </div>
            @endif

            {{-- 12. Ranking info --}}
            @if($event->ranking)
                <section>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Ranking</h2>
                    <div class="flex flex-wrap gap-4">
                        @if($event->ranking_coefficient)
                            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">Koeficient</div>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $event->ranking_coefficient }}</div>
                            </div>
                        @endif
                        @if($event->stages)
                            <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">Počet etap</div>
                                <div class="text-lg font-bold text-gray-900 dark:text-white">{{ $event->stages }}</div>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

        </div>
    </div>
@endsection
