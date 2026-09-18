@php
    use App\Enums\SportEventTransportType;
    use App\Filament\Resources\SportEvents\SportEventResource;
    use App\Models\SportEvent;
    use App\Services\Map\MapMarkerResolver;
    use App\Services\OrisApiService;
    use App\Shared\Helpers\AppHelper;
    use Carbon\Carbon;

    /** @var SportEvent $event */
    /** @var int $activeEntriesCount */
    /** @var int $transportOffersCount */
    /** @var int $transportFreeSeats */

    $hasMap = $event->gps_lat && $event->gps_lon;
    $hasMarkers = $event->sportEventMarkers->count() > 0;
    $markerResolver = new MapMarkerResolver();
    $hasCategories = $event->sportClasses->count() > 0;
    $hasNews = $event->sportEventNews->count() > 0;
    $hasLinks = $event->sportEventLinks->count() > 0;
    $hasServices = $event->sportServices->count() > 0;
    $hasWeather = !is_null($event->weather);
    $hasTransport = $event->transport_type !== SportEventTransportType::None;

    $entryDates = collect([
        1 => $event->entry_date_1,
        2 => $event->entry_date_2,
        3 => $event->entry_date_3,
    ])->filter();
    $nextDeadline = $entryDates->first(fn ($date) => Carbon::parse($date)->isFuture());
    $nextDeadlineNum = $entryDates->search($nextDeadline);
    $entryUrl = SportEventResource::getUrl('entry', ['record' => $event->id], panel: 'admin');
@endphp

@extends('layouts.terrain')

@section('title', $event->name)

@section('content')
    <section class="terrain-contours border-b border-terrain-line">
        <div class="mx-auto max-w-terrain px-4 py-8 sm:px-6 md:py-12">
            <nav aria-label="{{ __('sport-event.public.breadcrumb_label') }}" class="mb-6 flex min-w-0 flex-wrap items-center gap-2 text-sm text-terrain-secondary">
                <a href="{{ url('/') }}" class="inline-flex min-h-11 items-center hover:underline">{{ __('sport-event.public.breadcrumb_home') }}</a><span aria-hidden="true">/</span>
                <a href="{{ url('/#kalendar') }}" class="inline-flex min-h-11 items-center hover:underline">{{ __('sport-event.public.breadcrumb_events') }}</a><span aria-hidden="true">/</span>
                <span aria-current="page" class="min-w-0 break-words">{{ $event->name }}</span>
            </nav>
            <div class="mb-4 flex flex-wrap items-center gap-2">
                @if ($event->cancelled)<x-ui.badge tone="danger">{{ __('sport-event.public.cancelled_badge') }}</x-ui.badge>@endif
                <x-ui.badge>{{ __('sport-event.type_enum.' . $event->event_type->value) }}</x-ui.badge>
                @if ($event->sportDiscipline)<x-ui.badge>{{ $event->sportDiscipline->short_name }}</x-ui.badge>@endif
                @if ($event->sportLevel)<x-ui.badge>{{ $event->sportLevel->short_name }}</x-ui.badge>@endif
                @if (count($event->organization ?? []) > 0)<x-ui.badge>{{ Arr::join($event->organization, ', ') }}</x-ui.badge>@endif
            </div>
            <h1 class="max-w-3xl break-words text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl md:text-5xl">{{ $event->name }}</h1>
            @if ($event->alt_name)<p class="mt-3 text-lg text-terrain-secondary">{{ $event->alt_name }}</p>@endif
            <dl class="mt-6 flex flex-wrap gap-x-8 gap-y-4 text-sm">
                <div><dt class="text-terrain-secondary">{{ __('sport-event.public.label_date') }}</dt><dd class="mt-1 font-semibold"><time datetime="{{ $event->date->toDateString() }}">{{ $event->date->format(AppHelper::DATE_FORMAT) }}</time>@if ($event->date_end && $event->date_end->gt($event->date)) — <time datetime="{{ $event->date_end->toDateString() }}">{{ $event->date_end->format(AppHelper::DATE_FORMAT) }}</time>@endif</dd></div>
                @if ($event->start_time)<div><dt class="text-terrain-secondary">{{ __('sport-event.public.label_start') }}</dt><dd class="mt-1 font-semibold tabular-nums">{{ $event->start_time }}</dd></div>@endif
                @if ($event->place)<div><dt class="text-terrain-secondary">{{ __('sport-event.public.label_place') }}</dt><dd class="mt-1 font-semibold">{{ $event->place }}</dd></div>@endif
                @if ($event->oris_id)<div><dt class="text-terrain-secondary">ORIS</dt><dd class="mt-1"><a href="{{ OrisApiService::ORIS_URL }}/Zavod?id={{ $event->oris_id }}" target="_blank" rel="noopener noreferrer" class="font-semibold hover:underline">ORIS {{ $event->oris_id }} <span aria-hidden="true">↗</span><span class="sr-only">{{ __('sport-event.public.new_window') }}</span></a></dd></div>@endif
            </dl>
            <div class="mt-8 flex flex-wrap items-center gap-3">
                @unless ($event->cancelled)
                    <x-ui.button :href="$entryUrl" data-event-entry>{{ auth()->check() ? __('sport-event.public.manage_entries') : __('sport-event.public.login_for_entries') }} <span aria-hidden="true">→</span></x-ui.button>
                @endunless
                @if ($hasMap)<x-ui.button variant="secondary" href="#event-map">{{ __('sport-event.public.map_title') }} <span aria-hidden="true">↓</span></x-ui.button>@endif
                @if ($hasLinks)<x-ui.button variant="quiet" href="#event-documents">{{ __('sport-event.public.links_title') }} <span aria-hidden="true">↓</span></x-ui.button>@endif
            </div>
        </div>
    </section>
    <div class="mx-auto max-w-terrain px-4 py-8 sm:px-6 md:py-10">
        @if ($event->cancelled)
            <div role="status" class="mb-8 rounded-terrain-control border border-terrain-danger bg-terrain-danger-soft p-4 text-terrain-danger"><h2 class="font-bold">{{ __('sport-event.public.cancelled_title') }}</h2>@if ($event->cancelled_reason)<p class="mt-2 text-sm">{{ $event->cancelled_reason }}</p>@endif</div>
        @endif
        @if ($event->event_warning)
            <div class="terrain-prose mb-8 rounded-terrain-control border border-terrain-warning bg-terrain-warning-soft p-4 text-sm text-terrain-warning">{!! $event->event_warning !!}</div>
        @endif
        <dl class="grid grid-cols-2 gap-x-6 gap-y-6 border-y border-terrain-line py-6 lg:grid-cols-4">
            <div><dt class="text-sm text-terrain-secondary">{{ __('sport-event.public.stats_entries') }}</dt><dd class="mt-2 text-3xl font-bold tabular-nums">{{ $activeEntriesCount }}</dd><dd class="mt-1 text-xs text-terrain-secondary">{{ trans_choice('sport-event.public.stats_entries_hint', $activeEntriesCount) }}</dd></div>
            <div><dt class="text-sm text-terrain-secondary">{{ __('sport-event.public.stats_classes') }}</dt><dd class="mt-2 text-3xl font-bold tabular-nums">{{ $event->sportClasses->count() }}</dd><dd class="mt-1 text-xs text-terrain-secondary">{{ trans_choice('sport-event.public.stats_classes_hint', $event->sportClasses->count()) }}</dd></div>
            <div><dt class="text-sm text-terrain-secondary">{{ __('sport-event.public.stats_transport') }}</dt>
                @if ($hasTransport && $transportOffersCount > 0)<dd class="mt-2 text-3xl font-bold tabular-nums">{{ $transportOffersCount }}</dd><dd class="mt-1 text-xs text-terrain-secondary">{{ trans_choice('sport-event.public.stats_transport_hint', $transportFreeSeats, ['count' => $transportFreeSeats]) }}</dd>
                @elseif ($hasTransport)<dd class="mt-2 text-sm font-semibold">{{ __('sport-event.transport_type_enum.' . $event->transport_type->value) }}</dd><dd class="mt-1 text-xs text-terrain-secondary">{{ __('sport-event.public.stats_transport_no_offers') }}</dd>
                @else<dd class="mt-2 text-sm text-terrain-secondary">{{ __('sport-event.transport_type_enum.' . $event->transport_type->value) }}</dd>@endif
            </div>
            <div><dt class="text-sm text-terrain-secondary">{{ __('sport-event.public.stats_deadline') }}</dt>
                @if ($nextDeadline)<dd class="mt-2 text-lg font-bold">{{ Carbon::parse($nextDeadline)->format(AppHelper::DATE_FORMAT) }}</dd><dd class="mt-1 text-xs text-terrain-secondary">{{ __('sport-event.public.stats_deadline_term', ['term' => $nextDeadlineNum]) }} · {{ Carbon::parse($nextDeadline)->format('H:i') }}</dd>
                @elseif ($entryDates->isNotEmpty())<dd class="mt-2 text-sm font-semibold text-terrain-danger">{{ __('sport-event.public.stats_deadline_passed') }}</dd>
                @else<dd class="mt-2 text-sm text-terrain-secondary">{{ __('sport-event.public.stats_deadline_none') }}</dd>@endif
            </div>
        </dl>
        @if ($hasLinks)
            <section id="event-documents" class="border-b border-terrain-line py-8">
                <x-ui.section-heading>{{ __('sport-event.public.links_title') }}</x-ui.section-heading>
                <ul class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($event->sportEventLinks as $link)
                        <li class="min-w-0"><a href="{{ $link->source_url }}" target="_blank" rel="noopener noreferrer" class="flex min-h-16 items-center justify-between gap-4 rounded-terrain-control border border-terrain-line bg-terrain-surface px-4 py-3 hover:bg-terrain-muted"><span class="min-w-0 break-words text-sm font-semibold">{{ __('sport-event.type_enum_links.' . $link->source_type->value) }}</span><span aria-hidden="true">↗</span><span class="sr-only">{{ __('sport-event.public.new_window') }}</span></a></li>
                    @endforeach
                </ul>
            </section>
        @endif
        <div class="grid min-w-0 gap-10 pt-10 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
            <div class="min-w-0 space-y-8">
                @if ($event->event_info)
                    <section class="border-b border-terrain-line pb-8"><x-ui.section-heading>{{ __('sport-event.public.info_title') }}</x-ui.section-heading><div class="terrain-prose text-sm text-terrain-secondary">{!! $event->event_info !!}</div></section>
                @endif
                @include('pages.frontend.terrain.event-map')
                @if ($event->entry_desc || $entryDates->isNotEmpty())
                    <section id="event-entries" class="border-b border-terrain-line pb-8">
                        <x-ui.section-heading>{{ __('sport-event.public.entry_title') }}<x-slot:action><x-ui.badge>{{ $activeEntriesCount }}</x-ui.badge></x-slot:action></x-ui.section-heading>
                        <p class="text-sm text-terrain-secondary">{{ trans_choice('sport-event.public.entry_registered', $activeEntriesCount, ['count' => $activeEntriesCount]) }}</p>
                        @if ($entryDates->isNotEmpty())
                            <ol class="mt-5 space-y-2">
                                @foreach ($entryDates as $termNumber => $entryDate)
                                    @php
                                        $isPast = Carbon::parse($entryDate)->isPast();
                                        $isNext = $termNumber === $nextDeadlineNum;
                                        $increase = $termNumber === 2 ? $event->increase_entry_fee_2 : ($termNumber === 3 ? $event->increase_entry_fee_3 : null);
                                    @endphp
                                    <li @class(['flex flex-wrap items-center justify-between gap-x-4 gap-y-2 rounded-terrain-control border p-3 text-sm', 'border-terrain-accent bg-terrain-warning-soft' => $isNext, 'border-terrain-line' => !$isNext])>
                                        <span @class(['font-semibold', 'text-terrain-secondary line-through' => $isPast])>{{ __('sport-event.public.stats_deadline_term', ['term' => $termNumber]) }}</span>
                                        <span class="flex flex-wrap items-center gap-3">@if ($increase)<span class="text-xs font-semibold text-terrain-warning">+{{ $increase }} Kč</span>@endif<time datetime="{{ Carbon::parse($entryDate)->toIso8601String() }}" @class(['tabular-nums', 'text-terrain-secondary line-through' => $isPast])>{{ Carbon::parse($entryDate)->format(AppHelper::DATE_TIME_FORMAT) }}</time></span>
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                        @if ($event->entry_desc)<div class="terrain-prose mt-5 text-sm text-terrain-secondary">{!! $event->entry_desc !!}</div>@endif
                    </section>
                @endif
                @if ($hasServices)
                    <section class="min-w-0 border-b border-terrain-line pb-8">
                        <x-ui.section-heading>{{ __('sport-event.public.services_title') }}</x-ui.section-heading>
                        <div class="overflow-x-auto"><table class="w-full text-sm"><caption class="sr-only">{{ __('sport-event.public.services_title') }}</caption><thead><tr class="border-b border-terrain-line"><th scope="col" class="py-3 pr-4 text-left font-semibold">{{ __('sport-event.public.services_name') }}</th><th scope="col" class="px-4 py-3 text-right font-semibold">{{ __('sport-event.public.services_price') }}</th><th scope="col" class="py-3 pl-4 text-right font-semibold">{{ __('sport-event.public.services_available') }}</th></tr></thead><tbody>
                            @foreach ($event->sportServices as $service)<tr class="border-b border-terrain-line hover:bg-terrain-muted"><td class="py-3 pr-4 font-medium">{{ $service->service_name_cz }}</td><td class="whitespace-nowrap px-4 py-3 text-right tabular-nums text-terrain-secondary">{{ $service->unit_price }} Kč</td><td class="py-3 pl-4 text-right tabular-nums text-terrain-secondary">{{ $service->qty_remaining ?? $service->qty_available ?? '—' }}</td></tr>@endforeach
                        </tbody></table></div>
                    </section>
                @endif
                @if ($hasNews)
                    <section><x-ui.section-heading>{{ __('sport-event.public.news_title') }}<x-slot:action><x-ui.badge>{{ $event->sportEventNews->count() }}</x-ui.badge></x-slot:action></x-ui.section-heading>
                        <ol class="space-y-6">@foreach ($event->sportEventNews->sortByDesc('date') as $news)<li class="border-l-2 border-terrain-accent pl-4"><time datetime="{{ Carbon::parse($news->date)->toDateString() }}" class="text-xs font-semibold text-terrain-secondary">{{ Carbon::parse($news->date)->format(AppHelper::DATE_FORMAT) }}</time><div class="terrain-prose mt-2 text-sm">{!! $news->text !!}</div></li>@endforeach</ol>
                    </section>
                @endif
            </div>
            <aside aria-label="{{ __('sport-event.public.basic_title') }}" class="min-w-0 space-y-8 lg:border-l lg:border-terrain-line lg:pl-8">
                @if ($hasCategories)
                    <section class="border-b border-terrain-line pb-8"><x-ui.section-heading>{{ __('sport-event.public.classes_title') }}</x-ui.section-heading><p class="mb-4 text-sm text-terrain-secondary">{{ trans_choice('sport-event.public.classes_count', $event->sportClasses->count(), ['count' => $event->sportClasses->count()]) }}</p><div class="flex flex-wrap gap-2">@foreach ($event->sportClasses->sortBy('name') as $class)<x-ui.badge>{{ $class->name }}</x-ui.badge>@endforeach</div></section>
                @endif
                @if ($hasTransport)
                    <section class="rounded-terrain-panel border border-terrain-line bg-terrain-surface p-5"><h2 class="text-lg font-bold">{{ __('sport-event.public.transport_title') }}</h2><p class="mt-2 text-sm text-terrain-secondary">{{ __('sport-event.transport_type_enum.' . $event->transport_type->value) }}</p>
                        @if ($transportOffersCount > 0)<dl class="mt-5 grid grid-cols-2 gap-4"><div><dt class="text-xs text-terrain-secondary">{{ trans_choice('sport-event.public.transport_offers', $transportOffersCount) }}</dt><dd class="mt-1 text-2xl font-bold">{{ $transportOffersCount }}</dd></div><div><dt class="text-xs text-terrain-secondary">{{ trans_choice('sport-event.public.transport_free_seats', $transportFreeSeats) }}</dt><dd class="mt-1 text-2xl font-bold">{{ $transportFreeSeats }}</dd></div></dl>
                        @else<p class="mt-4 text-sm text-terrain-secondary">{{ __('sport-event.public.transport_no_offers') }}</p>@endif
                        <p class="mt-4 text-xs leading-relaxed text-terrain-secondary">{{ __('sport-event.public.transport_login_hint') }}</p><div class="mt-4"><x-ui.button variant="secondary" :href="$entryUrl">{{ __('sport-event.public.transport_action') }} <span aria-hidden="true">→</span></x-ui.button></div>
                    </section>
                @endif
                <section class="border-b border-terrain-line pb-8"><h2 class="text-lg font-bold">{{ __('sport-event.public.basic_title') }}</h2>@include('pages.frontend.terrain.event-facts')</section>
                @if ($hasWeather)
                    @php($weatherId = isset($event->weather['weather'][0]['id']) ? (int) $event->weather['weather'][0]['id'] : null)
                    <section><h2 class="text-lg font-bold">{{ __('sport-event.public.weather_title') }}</h2><div class="mt-4 flex items-center gap-4"><x-weather-icon :weather-id="$weatherId" class="size-12 shrink-0 text-terrain-warning"/><div><p class="text-3xl font-bold tabular-nums">{{ isset($event->weather['main']['temp']) ? round($event->weather['main']['temp'], 1) : '' }}&deg;C</p><p class="mt-1 text-sm text-terrain-secondary">{{ $event->weather['weather'][0]['description'] ?? '' }}</p></div></div></section>
                @endif
            </aside>
        </div>
    </div>
@endsection
