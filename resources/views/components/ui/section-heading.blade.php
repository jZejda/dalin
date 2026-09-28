<div {{ $attributes->class(['mb-6 flex flex-wrap items-center justify-between gap-4']) }}>
    <div class="flex items-center gap-4"><h2 class="text-3xl font-bold tracking-tight">{{ $slot }}</h2><span aria-hidden="true" class="h-1 w-14 rounded-full bg-terrain-accent"></span></div>
    @isset($action){{ $action }}@endisset
</div>
