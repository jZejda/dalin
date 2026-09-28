{{-- Terrain paginator: $paginator->links('pagination.terrain'). Compiled via @source in resources/css/terrain.css. --}}
@if ($paginator->hasPages())
    @php
        $item = 'inline-flex min-h-11 min-w-11 items-center justify-center gap-2 rounded-terrain-control px-3 text-sm font-semibold';
        $link = $item . ' border border-terrain-line bg-terrain-surface hover:bg-terrain-muted';
    @endphp
    <nav aria-label="{{ __('frontend.pagination.label') }}" class="mt-10 flex flex-wrap items-center justify-center gap-2">
        @if ($paginator->onFirstPage())
            <span aria-disabled="true" class="{{ $item }} border border-terrain-line text-terrain-secondary opacity-60">@svg('lucide-chevron-left', 'size-4', ['aria-hidden' => 'true'])<span class="max-sm:sr-only">{{ __('frontend.pagination.previous') }}</span></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $link }}">@svg('lucide-chevron-left', 'size-4', ['aria-hidden' => 'true'])<span class="max-sm:sr-only">{{ __('frontend.pagination.previous') }}</span></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span aria-hidden="true" class="{{ $item }} text-terrain-secondary">…</span>
            @else
                @foreach ($element as $page => $url)
                    @if ($page === $paginator->currentPage())
                        <span aria-current="page" aria-label="{{ __('frontend.pagination.page', ['page' => $page]) }}" class="{{ $item }} bg-terrain-accent text-terrain-on-accent">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" aria-label="{{ __('frontend.pagination.page', ['page' => $page]) }}" class="{{ $link }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $link }}"><span class="max-sm:sr-only">{{ __('frontend.pagination.next') }}</span>@svg('lucide-chevron-right', 'size-4', ['aria-hidden' => 'true'])</a>
        @else
            <span aria-disabled="true" class="{{ $item }} border border-terrain-line text-terrain-secondary opacity-60"><span class="max-sm:sr-only">{{ __('frontend.pagination.next') }}</span>@svg('lucide-chevron-right', 'size-4', ['aria-hidden' => 'true'])</span>
        @endif
    </nav>
@endif
