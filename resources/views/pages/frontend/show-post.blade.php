<?php
    use App\Models\Post;
    use App\Enums\ContentFormat;
    use Illuminate\Support\Carbon;
    /** @var Post $post */
?>

@extends('layouts.app')

@section('title', $post->title ?? '')

@section('content')
    <div class="py-4 md:py-8 bg-[url(https://abmbrno.cz/images/topography1.svg)] bg-slate-950 text-gray-700 dark:text-gray-300">
        <div class="container mx-auto">
            <div class="ml-3 text-2xl md:text-4xl bg-gradient-to-r from-yellow-400 to-amber-200 inline-block text-transparent bg-clip-text font-extrabold">
                @if($post->content_mode === ContentFormat::Html)
                    {!! $post->title !!}
                @elseif($post->content_mode === ContentFormat::Markdown)
                    {{ Markdown::parse($post->title) }}
                @endif
            </div>
        </div>
    </div>

    <div class="p-4 bg-white dark:bg-gray-900">
        <div class="container mx-auto app-front-content mb-10">
            @if($post->content_mode === ContentFormat::Html)
                <p>{!! $post->content !!}</p>
            @elseif($post->content_mode === ContentFormat::Markdown)
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
        </div>
    </div>
@endsection
