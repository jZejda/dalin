<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
    @forelse ($posts as $post)
        @php($cover = $post->coverUrl())
        <article wire:key="terrain-post-{{ $post->id }}" class="group relative flex min-w-0 flex-col overflow-hidden rounded-terrain-panel border border-terrain-line bg-terrain-surface transition-colors hover:bg-terrain-muted motion-reduce:transition-none">
            @if ($cover)
                <img src="{{ $cover }}" alt="" loading="lazy" class="aspect-[7/2] w-full object-cover">
            @else
                <div class="terrain-cover-placeholder aspect-[7/2] w-full" aria-hidden="true"></div>
            @endif
            <div class="flex flex-1 flex-col p-5">
                <h3 class="text-base font-bold leading-snug"><a href="{{ url('/novinka', $post->id) }}" class="decoration-terrain-accent underline-offset-4 after:absolute after:inset-0 group-hover:underline">{{ $post->title }}</a></h3>
                <div class="mt-auto flex items-center justify-between gap-4 pt-5 text-xs text-terrain-secondary">
                    <time datetime="{{ $post->created_at?->toIso8601String() }}">{{ $post->created_at?->format('d. m. Y') }}</time>
                    @svg('lucide-arrow-right', 'lucide-arrow-right size-4 shrink-0 text-terrain-ink transition-transform group-hover:translate-x-0.5 motion-reduce:transition-none', ['aria-hidden' => 'true'])
                </div>
            </div>
        </article>
    @empty
        <p class="text-sm text-terrain-secondary md:col-span-2 lg:col-span-3">Zatím tu nejsou žádné novinky. Brzy se dozvíte, co se v klubu děje.</p>
    @endforelse
</div>
