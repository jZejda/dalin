{{-- Light/dark toggle like dalin.cz: the sun shows in dark mode (switches to light), the moon in light mode. Wired up by x-ui.theme-script. --}}
<button type="button" data-terrain-theme-toggle
    data-label-light="{{ __('frontend.theme.switch_to_light') }}"
    data-label-dark="{{ __('frontend.theme.switch_to_dark') }}"
    aria-label="{{ __('frontend.theme.switch_to_dark') }}" title="{{ __('frontend.theme.switch_to_dark') }}"
    {{ $attributes->class(['inline-flex size-11 shrink-0 items-center justify-center rounded-terrain-control transition-colors hover:text-terrain-accent motion-reduce:transition-none']) }}>
    @svg('lucide-sun', 'hidden size-[22px] dark:block', ['aria-hidden' => 'true'])
    @svg('lucide-moon', 'size-[22px] dark:hidden', ['aria-hidden' => 'true'])
</button>
