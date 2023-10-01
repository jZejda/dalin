@php
    use Carbon\Carbon;
    use App\Models\Page

    /** @var Page $page */
    /** @var Page[] $relatedPages */
@endphp

@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

    <div class="container p-2 mx-auto">
        <div class="flex flex-row flex-wrap py-4">
            <aside class="w-full sm:w-1/3 md:w-1/4 px-2">
                <div class="sticky top-4 p-4 rounded-lg w-full bg-yellow-300 text-gray-800">
                    <ul class="space-y-1">
                        @foreach($relatedPages as $relatedPage)
                            <li>
                                <a href="{{$relatedPage->slug}}" class="flex items-center p-2 text-base text-gray-900 rounded-md dark:text-gray-700 hover:bg-yellow-200 dark:hover:bg-gray-700 dark:hover:text-gray-200 group">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-file-text"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                                    <span class="ml-3 tracking-tight uppercase">{{$relatedPage->title}}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>
            <main role="main" class="w-full sm:w-2/3 md:w-3/4 px-4 app-front-content">
                <h1 class="tracking-tight text-4xl font-black dark:text-gray-200">{{$page->title}}</h1>
                <article class="format">
                    <p class="dark:text-white">{{ Markdown::parse($page->content) }}</p>
                </article>
            </main>
        </div>
    </div>

@endsection
