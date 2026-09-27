@php
    use App\Enums\ContentFormat;
    use App\Models\Post;

    /** @var Post $post */
    $cover = $post->coverUrl('hero');
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
            <img src="{{ $cover }}" alt="" class="mb-8 aspect-[7/2] w-full rounded-terrain-panel border border-terrain-line object-cover md:mb-10">
        @endif

        <div class="terrain-prose">
            @if ($post->content_mode === ContentFormat::Html)
                {!! $post->content !!}
            @elseif ($post->content_mode === ContentFormat::Markdown)
                {{ Markdown::parse($post->content) }}
            @endif
        </div>

        <dl class="mt-10 flex flex-wrap gap-x-8 gap-y-4 text-sm">
            @if ($post->user)<div><dt class="text-terrain-secondary">{{ __('content.post.public.author') }}</dt><dd class="mt-1 font-semibold">{{ $post->user->name }}</dd></div>@endif
            @if ($post->created_at)<div><dt class="text-terrain-secondary">{{ __('content.post.public.published') }}</dt><dd class="mt-1 font-semibold"><time datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->format('d. m. Y') }}</time></dd></div>@endif
        </dl>
    </div>
@endsection
