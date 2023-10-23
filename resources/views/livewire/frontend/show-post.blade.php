@php
    use Carbon\Carbon;
    use App\Models\Post

    /** @var Post $post */
@endphp

@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="py-4 md:py-8 bg-[url(https://abmbrno.cz/images/topography1.svg)] bg-slate-950 text-gray-700 dark:text-gray-300">
        <div class="container mx-auto">
            <div class="ml-3 text-2xl md:text-4xl bg-gradient-to-r from-yellow-400 to-amber-200 inline-block text-transparent bg-clip-text font-extrabold">
                @if($post->content_mode === 1)
                    {!! $post->title !!}
                @elseif($post->content_mode === 2)
                    {{ Markdown::parse($post->title) }}
                @endif
            </div>
        </div>
    </div>

    <div class="p-4 bg-white dark:bg-gray-900">
        <div class="container mx-auto app-front-content mb-10">
            @if($post->content_mode === 1)
                <p>{!! $post->content !!}</p>
            @elseif($post->content_mode === 2)
                <p>{{ Markdown::parse($post->content) }}</p>
            @endif

            <div class="mt-10">
                <figcaption class="flex items-center mt-6 space-x-3">
                    <div class="flex items-center divide-x-2 divide-gray-300 dark:divide-gray-700">
                        <cite class="pr-3 font-medium text-gray-900 dark:text-white">{{  $post->user->name }}</cite>
                        <cite class="pl-3 text-sm font-light text-gray-500 dark:text-gray-400">{{ Carbon::createFromFormat('Y-m-d H:i:s', $post->created_at)->format('H:i - d.h.Y')  }}</cite>
                    </div>
                </figcaption>
            </div>


            {{-- Stop trying to control. --}}
        </div>
    </div>

@endsection

