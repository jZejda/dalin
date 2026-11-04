@props(['post'])
@php
    /** @var \App\Models\Post $post */
    $images = $post->galleryImages();
@endphp
@if ($images !== [])
    {{-- Thumbnails are real links to the large photo, so the gallery works without JS; Alpine upgrades them to a lightbox --}}
    <section
        {{ $attributes->class(['mt-10']) }}
        aria-labelledby="post-gallery-heading"
        x-data="{
            images: @js($images),
            index: null,
            show(i) { this.index = i; this.$nextTick(() => this.$refs.close.focus()); },
            hide() { const i = this.index; this.index = null; this.$nextTick(() => this.$refs.thumbs.querySelectorAll('a')[i]?.focus()); },
            step(d) { this.index = (this.index + d + this.images.length) % this.images.length; },
        }"
        @keydown.escape.window="index !== null && hide()"
        @keydown.arrow-left.window="index !== null && step(-1)"
        @keydown.arrow-right.window="index !== null && step(1)"
    >
        <h2 id="post-gallery-heading" class="mb-4 text-xl font-bold">{{ __('content.post.public.gallery_heading') }}</h2>

        <ul x-ref="thumbs" class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4" role="list">
            @foreach ($images as $i => $image)
                <li>
                    <a href="{{ $image['full'] }}" @click.prevent="show({{ $i }})" class="group block overflow-hidden rounded-terrain-panel border border-terrain-line bg-terrain-muted">
                        <img src="{{ $image['thumb'] }}" alt="{{ $image['alt'] }}" loading="lazy" width="800" height="600" class="aspect-[4/3] h-auto w-full object-cover transition-transform group-hover:scale-[1.03] motion-reduce:transition-none">
                    </a>
                </li>
            @endforeach
        </ul>

        <template x-teleport="body">
            <div
                x-show="index !== null"
                x-cloak
                x-transition.opacity
                role="dialog"
                aria-modal="true"
                :aria-label="index !== null ? images[index].alt : ''"
                class="terrain fixed inset-0 z-50 flex items-center justify-center bg-black/90 p-4"
                @click.self="hide()"
                x-trap.noscroll="index !== null"
            >
                <img :src="index !== null ? images[index].full : ''" :alt="index !== null ? images[index].alt : ''" class="max-h-[85vh] max-w-full rounded-terrain-panel object-contain">

                <p class="absolute left-1/2 top-4 -translate-x-1/2 text-sm text-white/80" aria-live="polite"
                   x-text="index !== null ? @js(__('content.post.public.gallery_counter')).replace(':current', index + 1).replace(':total', images.length) : ''"></p>

                <button type="button" x-ref="close" @click="hide()" class="absolute right-3 top-3 inline-flex size-11 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 focus-visible:outline-white!" aria-label="{{ __('content.post.public.gallery_close') }}">
                    @svg('lucide-x', 'size-6', ['aria-hidden' => 'true'])
                </button>

                <template x-if="images.length > 1">
                    <div>
                        <button type="button" @click="step(-1)" class="absolute left-3 top-1/2 inline-flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 focus-visible:outline-white!" aria-label="{{ __('content.post.public.gallery_previous') }}">
                            @svg('lucide-chevron-left', 'size-6', ['aria-hidden' => 'true'])
                        </button>
                        <button type="button" @click="step(1)" class="absolute right-3 top-1/2 inline-flex size-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20 focus-visible:outline-white!" aria-label="{{ __('content.post.public.gallery_next') }}">
                            @svg('lucide-chevron-right', 'size-6', ['aria-hidden' => 'true'])
                        </button>
                    </div>
                </template>
            </div>
        </template>
    </section>
@endif
