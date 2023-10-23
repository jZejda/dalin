@php
    use Carbon\Carbon;
    use App\Models\Page;
    use App\Shared\Entities\FrontendLinks;
    use App\Models\SportEventExport;

    /** @var Page $page */
    /** @var Page[] $relatedPages */
    /** @var FrontendLinks[] $relatedLinks */
@endphp

@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
    <div class="py-4 md:py-8 bg-[url(https://abmbrno.cz/images/topography1.svg)] bg-slate-950 text-gray-700 dark:text-gray-300">
        <div class="container mx-auto">
            <div class="ml-3 text-2xl md:text-4xl bg-gradient-to-r from-yellow-400 to-amber-200 inline-block text-transparent bg-clip-text font-extrabold">
                {{$page->title}}
            </div>
        </div>
    </div>

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
                        @if(count($relatedLinks) > 0)
                                <div class="relative flex py-5 items-center">
                                    <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                                    <span class="flex-shrink mx-4 text-gray-500">odkazy</span>
                                    <div class="flex-grow border-t border-gray-500 border-dashed"></div>
                                </div>
                            @foreach($relatedLinks as $relatedLink)
                                <li>
                                    <a href="{{$relatedLink->url}}" class="flex items-center p-2 text-base text-gray-900 rounded-md dark:text-gray-700 hover:bg-yellow-200 dark:hover:bg-gray-700 dark:hover:text-gray-200 group">
                                        @if($relatedLink->type === SportEventExport::ENTRY_LIST_CATEGORY)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-list"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                                        @elseif($relatedLink->type === SportEventExport::RESULT_LIST_CATEGORY)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-award"><circle cx="12" cy="8" r="7"/><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"/></svg>
                                        @endif
                                        <span class="ml-3 tracking-tight uppercase">{{$relatedLink->title}}</span>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </aside>
            <main role="main" class="w-full sm:w-2/3 md:w-3/4 px-4 app-front-content">
                <article class="pt-0">
                    <p class="dark:text-white">{{ Markdown::parse($page->content) }}</p>
                </article>
            </main>
        </div>
    </div>

@endsection
