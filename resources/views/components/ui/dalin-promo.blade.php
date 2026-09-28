@php
    $features = [
        'oris' => 'lucide-calendar-check',
        'finance' => 'lucide-wallet',
        'web' => 'lucide-globe',
    ];
@endphp
{{-- Always-dark DaLin band: the product screenshot bleeds off the right edge, framed by a control-circle ring. --}}
<section aria-label="{{ __('frontend.dalin_promo.label') }}" {{ $attributes->class(['mx-auto max-w-terrain px-4 py-terrain-section sm:px-6']) }}>
    <div class="relative isolate overflow-hidden rounded-terrain-panel border border-white/10 bg-terrain-nav text-terrain-on-nav">
        <div class="grid gap-10 px-6 pt-10 sm:px-10 sm:pt-12 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)] lg:gap-6 lg:py-16 lg:pl-14">
            <div class="relative">
                <x-ui.dalin-logo class="w-32 text-terrain-on-nav sm:w-36" />
                <p class="mt-2 text-sm text-white/65">{{ __('frontend.dalin_promo.tagline') }}</p>

                <h2 class="mt-8 max-w-md text-4xl font-extrabold leading-[1.05] tracking-tight sm:text-5xl">{{ __('frontend.dalin_promo.heading') }}</h2>
                <p class="mt-5 max-w-md leading-relaxed text-white/75">{{ __('frontend.dalin_promo.lead') }}</p>

                <ul class="mt-8 space-y-5">
                    @foreach ($features as $key => $icon)
                        <li class="flex gap-4">
                            <span class="flex size-10 shrink-0 items-center justify-center rounded-terrain-control bg-terrain-accent/15 text-terrain-accent">
                                @svg($icon, 'size-5', ['aria-hidden' => 'true'])
                            </span>
                            <span>
                                <span class="block font-semibold">{{ __('frontend.dalin_promo.features.' . $key . '.title') }}</span>
                                <span class="block text-sm text-white/65">{{ __('frontend.dalin_promo.features.' . $key . '.text') }}</span>
                            </span>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-9 flex flex-wrap items-center gap-x-6 gap-y-3">
                    <x-ui.button href="https://dalin.cz" target="_blank" rel="noopener noreferrer" class="focus-visible:outline-terrain-accent!">
                        {{ __('frontend.dalin_promo.cta_explore') }}
                        @svg('lucide-arrow-up-right', 'size-4', ['aria-hidden' => 'true'])
                        <span class="sr-only">{{ __('frontend.dalin_promo.new_window') }}</span>
                    </x-ui.button>
                    <a href="https://docs.dalin.cz" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center gap-2 text-sm font-semibold underline decoration-terrain-accent underline-offset-4 hover:text-terrain-accent focus-visible:outline-terrain-accent!">
                        {{ __('frontend.dalin_promo.cta_docs') }}
                        @svg('lucide-arrow-up-right', 'size-4', ['aria-hidden' => 'true'])
                        <span class="sr-only">{{ __('frontend.dalin_promo.new_window') }}</span>
                    </a>
                </div>
            </div>

            <div class="relative -mr-6 self-end sm:-mr-10 lg:-mr-40 lg:self-center">
                <div aria-hidden="true" class="pointer-events-none absolute left-1/2 top-1/2 -z-10 size-[20rem] -translate-x-1/2 -translate-y-1/2 rounded-full border-2 border-terrain-accent/70 sm:size-[28rem] lg:left-[45%] lg:size-[34rem]"></div>
                <figure class="relative overflow-hidden rounded-t-xl border border-white/15 bg-terrain-surface shadow-2xl shadow-black/40 lg:rotate-[-2deg] lg:rounded-xl">
                    <div aria-hidden="true" class="flex h-9 items-center gap-3 border-b border-terrain-line bg-terrain-muted px-4 text-xs font-semibold text-terrain-secondary">
                        <span class="flex gap-1.5"><span class="size-2 rounded-full bg-terrain-line"></span><span class="size-2 rounded-full bg-terrain-line"></span><span class="size-2 rounded-full bg-terrain-line"></span></span>
                        <span class="truncate">dalin / Závody a události</span>
                    </div>
                    <img src="{{ asset('images/dalin/races-light.webp') }}" alt="{{ __('frontend.dalin_promo.screenshot_alt') }}" width="1600" height="898" loading="lazy" class="block h-auto w-full dark:hidden">
                    <img src="{{ asset('images/dalin/races-dark.webp') }}" alt="{{ __('frontend.dalin_promo.screenshot_alt') }}" width="1600" height="898" loading="lazy" class="hidden h-auto w-full dark:block">
                </figure>
            </div>
        </div>
    </div>
</section>
