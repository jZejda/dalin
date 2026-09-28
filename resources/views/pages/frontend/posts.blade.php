@php
    use Illuminate\Pagination\LengthAwarePaginator;

    /** @var LengthAwarePaginator<int, \App\Models\Post> $posts */
    $title = $posts->currentPage() > 1
        ? __('content.post.public.index_page_title', ['page' => $posts->currentPage()])
        : __('content.post.public.breadcrumb_news');
@endphp

@extends('layouts.terrain')

@section('title', $title)

@section('content')
    <section class="terrain-contours">
        <div class="mx-auto max-w-terrain px-4 py-8 sm:px-6 md:py-12">
            <nav aria-label="{{ __('content.post.public.breadcrumb_label') }}" class="mb-6 flex min-w-0 flex-wrap items-center gap-2 text-sm text-terrain-secondary">
                <a href="{{ url('/') }}" class="inline-flex min-h-11 items-center hover:underline">{{ __('content.post.public.breadcrumb_home') }}</a><span aria-hidden="true">/</span>
                <span aria-current="page">{{ __('content.post.public.breadcrumb_news') }}</span>
            </nav>
            <h1 class="text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl md:text-5xl">{{ __('content.post.public.breadcrumb_news') }}</h1>
            <p class="mt-4 max-w-xl text-lg text-terrain-secondary">{{ __('content.post.public.index_lead') }}</p>
        </div>
    </section>

    <div class="mx-auto max-w-terrain px-4 pb-terrain-section pt-8 sm:px-6 md:pt-10">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($posts as $post)
                <x-ui.post-card :post="$post" heading="h2" />
            @empty
                <p class="text-sm text-terrain-secondary md:col-span-2 lg:col-span-3">{{ __('content.post.public.index_empty') }}</p>
            @endforelse
        </div>

        {{ $posts->links('pagination.terrain') }}
    </div>
@endsection
