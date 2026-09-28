@props(['date', 'accent' => true])
<time datetime="{{ $date->toDateString() }}" {{ $attributes->class(['flex size-[4.5rem] shrink-0 flex-col items-center justify-center rounded-terrain-panel text-center', 'bg-terrain-accent text-terrain-on-accent' => $accent, 'bg-terrain-muted text-terrain-ink' => !$accent]) }}>
    <span class="sr-only">{{ $date->format('d. m. Y') }}</span>
    <span aria-hidden="true" class="w-12 text-3xl font-extrabold leading-none tabular-nums">{{ $date->format('d.') }}</span>
    <span aria-hidden="true" class="mt-1.5 w-12 text-lg font-bold leading-none tracking-wide">{{ __('sport-event.public.month_short.' . $date->month) }}</span>
</time>
