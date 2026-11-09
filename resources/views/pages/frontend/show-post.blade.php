@php
    use App\Enums\ContentFormat;
    use App\Models\Post;

    /** @var Post $post */
    $cover = $post->coverUrl('detail');
@endphp

@extends('layouts.terrain')

@section('title', $post->title ?? '')

@section('content')
    <section class="terrain-contours">
        <div class="mx-auto max-w-terrain px-4 py-8 sm:px-6 md:py-12">
            <nav aria-label="{{ __('content.post.public.breadcrumb_label') }}" class="mb-6 flex min-w-0 flex-wrap items-center gap-2 text-sm text-terrain-secondary">
                <a href="{{ url('/') }}" class="inline-flex min-h-11 items-center hover:underline">{{ __('content.post.public.breadcrumb_home') }}</a><span aria-hidden="true">/</span>
                <a href="{{ route('posts.index') }}" class="inline-flex min-h-11 items-center hover:underline">{{ __('content.post.public.breadcrumb_news') }}</a><span aria-hidden="true">/</span>
                <span aria-current="page" class="min-w-0 break-words">{{ $post->title }}</span>
            </nav>
            <h1 class="max-w-3xl break-words text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl md:text-5xl">{{ $post->title }}</h1>
        </div>
    </section>

    <div class="mx-auto max-w-terrain px-4 py-8 sm:px-6 md:py-10">
        @if ($cover)
            <img src="{{ $cover }}" alt="" class="mx-auto mb-8 block h-auto max-h-[70vh] w-auto max-w-full rounded-terrain-panel border border-terrain-line md:mb-10">
        @endif

        <div class="terrain-prose">
            @if ($post->content_mode === ContentFormat::Html)
                {!! $post->content !!}
            @elseif ($post->content_mode === ContentFormat::Markdown)
                {{ Markdown::parse($post->content) }}
            @endif
        </div>

        <footer class="mt-10 flex flex-wrap items-center justify-between gap-x-8 gap-y-2 border-t border-terrain-line pt-5 text-sm md:mt-12">
            <dl class="flex flex-wrap gap-x-6 gap-y-2">
                @if ($post->user)
                    <div class="flex min-h-11 items-center gap-2">
                        <dt class="text-terrain-secondary">@svg('lucide-user-round', 'size-4', ['aria-hidden' => 'true'])<span class="sr-only">{{ __('content.post.public.author') }}</span></dt>
                        <dd class="font-semibold">{{ $post->user->name }}</dd>
                    </div>
                @endif
                @if ($post->created_at)
                    <div class="flex min-h-11 items-center gap-2">
                        <dt class="text-terrain-secondary">@svg('lucide-calendar-days', 'size-4', ['aria-hidden' => 'true'])<span class="sr-only">{{ __('content.post.public.published') }}</span></dt>
                        <dd class="text-terrain-secondary"><time datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->format('j. n. Y') }}</time></dd>
                    </div>
                @endif
            </dl>
            <a href="{{ route('posts.index') }}" class="inline-flex min-h-11 items-center gap-2 font-semibold hover:underline">
                @svg('lucide-arrow-left', 'size-4 text-terrain-secondary', ['aria-hidden' => 'true'])
                {{ __('content.post.public.more_link') }}
            </a>
        </footer>
    </div>
@endsection
