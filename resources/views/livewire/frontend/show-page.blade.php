@php
    use Carbon\Carbon;
    use App\Models\Page

    /** @var Page $page */
@endphp

@extends('layouts.app')

@section('title', $page->title)

@section('content')

<div class="py-4 md:py-8 bg-[url(https://abmbrno.cz/images/topography1.svg)] bg-slate-950 text-gray-700 dark:text-gray-300">
    <div class="container mx-auto">
        <div class="ml-3 text-2xl md:text-4xl bg-gradient-to-r from-yellow-400 to-amber-200 inline-block text-transparent bg-clip-text font-extrabold">
            @if($page->content_format === 1)
                {!! $page->title !!}
            @elseif($page->content_format === 2)
                {{ Markdown::parse($page->title) }}
            @endif
        </div>

    </div>
</div>

<div class="container mx-auto app-front-content mb-10">
    <div class="mx-5">
        @if($page->content_format === 1)
            <p>{!! $page->content !!}</p>
        @elseif($page->content_format === 2)
            <p>{{ Markdown::parse($page->content) }}</p>
        @endif
    </div>Na stránkách závodu byly zveřejneny ukázky mapy.

    <div class="mt-10 mx-5">
        <figcaption class="flex items-center mt-6 space-x-3">
            <div class="flex items-center divide-x-2 divide-gray-300 dark:divide-gray-700">
                <cite class="pr-3 font-medium text-gray-900 dark:text-white">{{  $page->user->name }}</cite>
                <cite class="pl-3 text-sm font-light text-gray-500 dark:text-gray-400">{{ Carbon::createFromFormat('Y-m-d H:i:s', $page->created_at)->format('H:i - d.h.Y')  }}</cite>
            </div>
        </figcaption>
    </div>
</div>
@endsection
