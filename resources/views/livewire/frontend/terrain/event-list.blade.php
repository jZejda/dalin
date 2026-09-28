@php
    use App\Services\OrisApiService;
@endphp
<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    @forelse ($events as $event)
        @php
            $clubEvent = in_array(config('site-config.club.abbr'), $event->organization ?? [], true);
            $meta = array_filter([
                $event->organization ? Arr::join($event->organization, ', ') : null,
                $event->region ? Arr::join($event->region, ', ') : null,
            ]);
        @endphp
        <article wire:key="terrain-event-{{ $event->id }}" @class(['group relative flex min-w-0 gap-4 rounded-terrain-panel border bg-terrain-surface p-4 transition-colors hover:bg-terrain-muted motion-reduce:transition-none', 'border-terrain-accent' => $clubEvent, 'border-terrain-line' => !$clubEvent])>
            <x-ui.event-date :date="$event->date" :accent="$clubEvent" />
            <div class="flex min-w-0 flex-1 flex-col">
                <h3 class="text-lg font-bold leading-snug"><a href="{{ route('sport-event.show', $event->id) }}" class="decoration-terrain-accent underline-offset-4 after:absolute after:inset-0 hover:underline">{{ $event->name }}</a></h3>
                @if ($event->alt_name)<p class="mt-1 text-base leading-snug text-terrain-secondary">{{ $event->alt_name }}</p>@endif
                @if ($event->place)
                    <p class="mt-3 flex min-w-0 items-center gap-2 text-sm text-terrain-secondary">
                        @svg('lucide-map-pin', 'lucide-map-pin size-4 shrink-0', ['aria-hidden' => 'true'])
                        <span class="min-w-0 truncate" title="{{ $event->place }}">{{ mb_strlen($event->place) > 30 ? mb_substr($event->place, 0, 29) . '…' : $event->place }}</span>
                    </p>
                @endif
                <div class="mt-auto flex items-end justify-between gap-3 pt-2">
                    <p class="flex min-w-0 flex-wrap items-center gap-x-2 text-sm font-semibold">
                        @foreach ($meta as $item)
                            <span>{{ $item }}</span>
                            @if (!$loop->last || $event->oris_id)<span aria-hidden="true" class="text-terrain-secondary">·</span>@endif
                        @endforeach
                        @if ($event->oris_id)<a href="{{ OrisApiService::ORIS_URL }}/Zavod?id={{ $event->oris_id }}" target="_blank" rel="noopener noreferrer" class="relative inline-flex items-center py-1 hover:underline after:absolute after:inset-x-0 after:-inset-y-2.5">ORIS {{ $event->oris_id }} <span class="sr-only">(nové okno)</span></a>@endif
                    </p>
                    @svg('lucide-arrow-right', 'lucide-arrow-right size-5 shrink-0 transition-transform group-hover:translate-x-0.5 motion-reduce:transition-none', ['aria-hidden' => 'true'])
                </div>
            </div>
        </article>
    @empty
        <p class="text-sm text-terrain-secondary md:col-span-2 lg:col-span-3">Právě nejsou naplánované žádné nadcházející akce. Sledujte novinky z klubu.</p>
    @endforelse
</div>
