@props(['date'])
<time datetime="{{ $date->toDateString() }}" {{ $attributes->class(['flex size-[4.5rem] shrink-0 flex-col items-center justify-center rounded-terrain-panel bg-terrain-accent text-center text-terrain-on-accent']) }}>
    <span class="sr-only">{{ $date->format('d. m. Y') }}</span>
    <span aria-hidden="true" class="w-12 text-3xl font-extrabold leading-none tabular-nums">{{ $date->format('d.') }}</span>
    <span aria-hidden="true" class="mt-1.5 w-12 text-lg font-bold leading-none tracking-wide">{{ __('sport-event.public.month_short.' . $date->month) }}</span>
</time>
