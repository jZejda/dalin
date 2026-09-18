@php($logo = 'filament.logo.' . strtolower((string) config('site-config.club.abbr')) . '-logo')
<a href="{{ url('/') }}" {{ $attributes->class(['inline-flex min-h-11 items-center gap-3']) }}>
    <span class="flex size-9 shrink-0 items-center justify-center" aria-hidden="true">
        @if (View::exists($logo))
            @include($logo)
        @else
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="size-8"><circle cx="12" cy="12" r="9"/><path d="m16 8-3 5-5 3 3-5Z"/></svg>
        @endif
    </span>
    <span class="min-w-0"><span class="block text-base font-extrabold tracking-tight">{{ config('site-config.club.abbr') }}</span><span class="block text-xs opacity-80">{{ config('site-config.club.full_name') }}</span></span>
</a>
