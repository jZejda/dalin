<div {{ $attributes->class(['mb-6 flex flex-wrap items-center justify-between gap-4']) }}>
    <div class="flex items-center gap-4"><h2 class="text-2xl font-bold tracking-tight">{{ $slot }}</h2><span aria-hidden="true" class="h-0.5 w-10 bg-terrain-accent"></span></div>
    @isset($action){{ $action }}@endisset
</div>
