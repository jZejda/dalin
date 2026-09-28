@php
    use App\Shared\Helpers\AppHelper;
    use App\Services\OrisApiService;
@endphp
                <dl class="mt-4 space-y-3 text-sm">

                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-terrain-secondary">{{ __('sport-event.public.label_date') }}</dt>
                        <dd class="text-right font-medium text-terrain-ink">
                            {{ $event->date->format(AppHelper::DATE_FORMAT) }}
                            @if($event->date_end && $event->date_end->gt($event->date))
                                — {{ $event->date_end->format(AppHelper::DATE_FORMAT) }}
                            @endif
                        </dd>
                    </div>

                    @if($event->start_time)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-terrain-secondary">{{ __('sport-event.public.label_start') }}</dt>
                        <dd class="text-right font-medium tabular-nums text-terrain-ink">{{ $event->start_time }}</dd>
                    </div>
                    @endif

                    @if($event->place)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-terrain-secondary">{{ __('sport-event.public.label_place') }}</dt>
                        <dd class="text-right font-medium text-terrain-ink">{{ $event->place }}</dd>
                    </div>
                    @endif

                    @if($hasMap)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-terrain-secondary">{{ __('sport-event.public.label_gps') }}</dt>
                        <dd class="text-right">
                            <a href="https://www.google.com/maps?q={{ $event->gps_lat }},{{ $event->gps_lon }}"
                               target="_blank" rel="noopener noreferrer"
                               class="font-medium tabular-nums text-terrain-ink transition-colors hover:text-terrain-warning"
                               >
                                {{ number_format((float)$event->gps_lat, 5) }}, {{ number_format((float)$event->gps_lon, 5) }}
                            </a>
                        </dd>
                    </div>
                    @endif

                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-terrain-secondary">{{ __('sport-event.public.label_type') }}</dt>
                        <dd class="text-right font-medium text-terrain-ink">{{ __('sport-event.type_enum.' . $event->event_type->value) }}</dd>
                    </div>

                    @if($event->sportDiscipline)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-terrain-secondary">{{ __('sport-event.public.label_discipline') }}</dt>
                        <dd class="text-right font-medium text-terrain-ink">{{ $event->sportDiscipline->short_name }}</dd>
                    </div>
                    @endif

                    @if($event->sportLevel)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-terrain-secondary">{{ __('sport-event.public.label_level') }}</dt>
                        <dd class="text-right font-medium text-terrain-ink">{{ $event->sportLevel->short_name }}</dd>
                    </div>
                    @endif

                    @if(count($event->organization ?? []) > 0)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-terrain-secondary">{{ __('sport-event.public.label_organizer') }}</dt>
                        <dd class="text-right font-medium text-terrain-ink">{{ Arr::join($event->organization, ', ') }}</dd>
                    </div>
                    @endif

                    @if(count($event->region ?? []) > 0)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-terrain-secondary">{{ __('sport-event.public.label_region') }}</dt>
                        <dd class="text-right font-medium text-terrain-ink">{{ Arr::join($event->region, ', ') }}</dd>
                    </div>
                    @endif

                    @if($event->ranking_coefficient)
                    <div class="flex items-start justify-between gap-3">
                        <dt class="shrink-0 text-terrain-secondary">{{ __('sport-event.public.label_ranking') }}</dt>
                        <dd class="text-right font-medium tabular-nums text-terrain-ink">{{ $event->ranking_coefficient }}</dd>
                    </div>
                    @endif

                    @if($event->oris_id)
                    <div class="flex items-start justify-between gap-3 border-t border-terrain-line pt-3">
                        <dt class="shrink-0 text-terrain-secondary">ORIS</dt>
                        <dd class="text-right">
                            <a href="{{ OrisApiService::ORIS_URL }}/Zavod?id={{ $event->oris_id }}"
                               target="_blank" rel="noopener noreferrer"
                               class="inline-flex items-center gap-1 font-medium text-terrain-warning transition-colors hover:text-terrain-ink"
                               >
                                {{ $event->oris_id }}
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-2.5 w-2.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                </svg>
                            </a>
                        </dd>
                    </div>
                    @endif

                </dl>
