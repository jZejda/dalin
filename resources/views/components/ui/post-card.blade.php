@props(['post', 'heading' => 'h3'])
@php
    /** @var \App\Models\Post $post */
    $cover = $post->coverUrl();
    $heading = in_array($heading, ['h2', 'h3'], true) ? $heading : 'h3';
@endphp
<article {{ $attributes->class(['group relative flex min-w-0 flex-col overflow-hidden rounded-terrain-panel border border-terrain-line bg-terrain-surface transition-colors hover:bg-terrain-muted motion-reduce:transition-none']) }}>
    @if ($cover)
        <img src="{{ $cover }}" alt="" loading="lazy" class="aspect-[4/3] w-full object-cover">
    @else
        <div class="terrain-cover-placeholder aspect-[4/3] w-full" aria-hidden="true"></div>
    @endif
    <div class="flex flex-1 flex-col p-5">
        <{{ $heading }} class="text-base font-bold leading-snug"><a href="{{ url('/novinka', $post->id) }}" class="decoration-terrain-accent underline-offset-4 after:absolute after:inset-0 group-hover:underline">{{ $post->title }}</a></{{ $heading }}>
        <div class="mt-auto flex items-center justify-between gap-4 pt-5 text-xs text-terrain-secondary">
            <p class="flex min-w-0 flex-wrap items-center gap-x-2">
                @if ($post->user)<span class="truncate font-semibold text-terrain-ink">{{ $post->user->name }}</span><span aria-hidden="true">·</span>@endif
                <time datetime="{{ $post->created_at?->toIso8601String() }}">{{ $post->created_at?->format('d. m. Y') }}</time>
            </p>
            @svg('lucide-arrow-right', 'lucide-arrow-right size-4 shrink-0 text-terrain-ink transition-transform group-hover:translate-x-0.5 motion-reduce:transition-none', ['aria-hidden' => 'true'])
        </div>
    </div>
</article>
