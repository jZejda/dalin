@php
    use App\Enums\ContentFormat;
@endphp
<div class="grid gap-x-8 gap-y-8 md:grid-cols-2 lg:grid-cols-3">
    @forelse ($posts as $post)
        <article wire:key="terrain-post-{{ $post->id }}" class="flex min-w-0 flex-col border-b border-terrain-line pb-6">
            @if ($post->img_url)
                <a href="{{ url('/novinka', $post->id) }}" class="mb-4 block" tabindex="-1" aria-hidden="true"><img src="{{ $post->img_url }}" alt="" loading="lazy" class="aspect-[16/9] w-full rounded-terrain-control object-cover"></a>
            @endif
            <p class="mb-3 text-xs text-terrain-secondary"><time datetime="{{ $post->created_at?->toIso8601String() }}">{{ $post->created_at?->format('d. m. Y') }}</time></p>
            <h3 class="text-xl font-bold leading-snug tracking-tight"><a href="{{ url('/novinka', $post->id) }}" class="hover:underline decoration-terrain-accent underline-offset-4">{{ $post->title }}</a></h3>
            @if ($post->editorial !== null)
                @php
                    $preview = match ($post->content_mode) {
                        ContentFormat::Html => $post->editorial,
                        ContentFormat::Markdown => (string) Markdown::parse($post->editorial),
                        default => '',
                    };
                    $preview = html_entity_decode(strip_tags($preview), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                @endphp
                @if (trim($preview) !== '')
                    <p class="mt-3 text-sm leading-relaxed text-terrain-secondary">{{ Str::limit(trim($preview), 240) }}</p>
                @endif
            @endif
            <div class="mt-auto flex items-center justify-between gap-4 pt-5 text-xs text-terrain-secondary"><span>{{ $post->user?->name }}</span><a href="{{ url('/novinka', $post->id) }}" class="inline-flex min-h-11 items-center font-semibold text-terrain-ink hover:underline" aria-label="Číst novinku: {{ $post->title }}">Číst dál <span class="ml-2" aria-hidden="true">→</span></a></div>
        </article>
    @empty
        <p class="text-sm text-terrain-secondary md:col-span-2 lg:col-span-3">Zatím tu nejsou žádné novinky. Brzy se dozvíte, co se v klubu děje.</p>
    @endforelse
</div>
