@php
    use Carbon\Carbon;
    use App\Models\Post

    /** @var Post $post */
@endphp

@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="pt-4 bg-white dark:bg-gray-900">
        <div class="container mx-auto app-front-content mb-10">
            @if($post->content_mode === 1)
                <h2>{!! $post->title !!}</h2>
                <div>{!! $post->content !!}</div>
            @elseif($post->content_mode === 2)
                <p>{{ Markdown::parse($post->title) }}</p>
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

