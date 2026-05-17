@php
    use App\Models\Page;
    use App\Enums\ContentFormat;
    use App\Enums\SportEventExportsType;
    use App\Shared\Entities\FrontendLinks;
    use Filament\Forms\Components\RichEditor\RichContentRenderer;
    use App\Filament\Forms\Components\RichEditor\RichContentCustomBlocks\RichContentBlocks;

    /** @var Page $page */
    /** @var \Illuminate\Support\Collection $relatedPages */
    /** @var FrontendLinks[] $relatedLinks */

    $category = $page->contentCategory;
    $sportEvent = $category?->sportEvent;
@endphp

@extends('layouts.app')

@section('title', $page->title)

@section('content')

{{-- Yellow accent line — separator between tabs and content --}}
<div class="h-0.5 bg-yellow-400 dark:bg-yellow-500"></div>

{{-- Light header --}}
<div class="bg-white dark:bg-gray-900 pt-4 pb-0">
    <div class="container mx-auto px-4">

        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-2">
            <a href="/" class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors">Domů</a>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
            @if($sportEvent)
                <a href="{{ route('sport-event.show', $sportEvent->id) }}"
                   class="hover:text-gray-700 dark:hover:text-gray-200 transition-colors"
                   style="text-decoration: none !important;">
                    {{ $sportEvent->name }}
                </a>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            @elseif($category && $category->title !== 'Nezařazeno')
                <span>{{ $category->title }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                </svg>
            @endif
            <span class="font-semibold text-gray-700 dark:text-gray-200 truncate">{{ $page->title }}</span>
        </nav>

        {{-- Title row --}}
        <div class="flex flex-wrap items-end gap-3 pb-3">
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800 dark:text-gray-100 leading-tight tracking-tight">
                {{ $category?->title ?? $page->title }}
            </h1>
            @if($sportEvent)
                <div class="flex flex-wrap items-center gap-3 text-sm text-gray-500 dark:text-gray-400 pb-0.5">
                    @if($sportEvent->date)
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                            </svg>
                            {{ $sportEvent->date->format('j. n. Y') }}
                        </span>
                    @endif
                    @if($sportEvent->place)
                        <span class="flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 0 1 15 0Z" />
                            </svg>
                            {{ $sportEvent->place }}
                        </span>
                    @endif
                </div>
            @endif
        </div>

    </div>
    <div class="border-b border-gray-200 dark:border-gray-700 mt-3"></div>
</div>

{{-- Sticky tab bar --}}
<div class="sticky top-0 z-10 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
    <div class="container mx-auto px-4">
        {{-- overflow-x-auto + hidden scrollbar = horizontal swipe on mobile --}}
        <div class="flex overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">

            {{-- Page tabs --}}
            @foreach($relatedPages as $relatedPage)
                @php $isActive = $relatedPage->slug === $page->slug; @endphp
                <a href="/stranka/{{ $relatedPage->slug }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-3 text-sm whitespace-nowrap -mb-px border-b-2 transition-colors duration-150
                          {{ $isActive
                              ? 'font-bold text-gray-900 dark:text-white border-amber-400'
                              : 'font-medium text-gray-500 dark:text-gray-400 border-transparent hover:text-gray-700 dark:hover:text-gray-200' }}"
                   style="text-decoration: none !important;">
                    {{ $relatedPage->title }}
                </a>
            @endforeach

            {{-- Visual separator before external links --}}
            @if(count($relatedLinks) > 0 && $relatedPages->count() > 0)
                <div class="w-px bg-gray-200 dark:bg-gray-700 my-2 mx-2 shrink-0 self-stretch"></div>
            @endif

            {{-- External links as tabs (startovka, výsledky) --}}
            @foreach($relatedLinks as $relatedLink)
                <a href="{{ $relatedLink->url }}"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 px-3.5 py-3 text-sm font-medium whitespace-nowrap -mb-px border-b-2 border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors duration-150"
                   style="text-decoration: none !important;">
                    @if($relatedLink->type === SportEventExportsType::EventEntryListCat)
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 0 1 3 3h-15a3 3 0 0 1 3-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 0 1-.982-3.172M9.497 14.25a7.454 7.454 0 0 1 .981-3.172M5.25 4.236c-.982.143-1.954.317-2.916.52A6.003 6.003 0 0 0 7.73 9.728M5.25 4.236V4.5c0 2.108.966 3.99 2.48 5.228M5.25 4.236V2.721C7.456 2.41 9.71 2.25 12 2.25c2.291 0 4.545.16 6.75.47v1.516M7.73 9.728a6.726 6.726 0 0 0 2.748 1.35m8.272-6.842V4.5c0 2.108-.966 3.99-2.48 5.228m2.48-5.492a46.32 46.32 0 0 1 2.916.52 6.003 6.003 0 0 1-5.395 4.972m0 0a6.726 6.726 0 0 1-2.749 1.35m0 0a6.772 6.772 0 0 1-3.044 0" />
                        </svg>
                    @endif
                    {{ $relatedLink->title }}
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                </a>
            @endforeach

        </div>
    </div>
</div>

{{-- Page content --}}
<div class="container mx-auto px-4 py-6 app-front-content">
    <article>
        @if($page->content_format === ContentFormat::Html)
            <div class="prose dark:prose-invert max-w-none dark:text-white">{!! $page->content !!}</div>
        @elseif($page->content_format === ContentFormat::TipTapJson)
            {!! RichContentRenderer::make($page->content)
                ->customBlocks(RichContentBlocks::all())
                ->fileAttachmentsDisk('rich-editor-attachments')
                ->fileAttachmentsVisibility('public')
                ->toUnsafeHtml() !!}
        @else
            <div class="prose dark:prose-invert max-w-none dark:text-white">{{ Markdown::parse($page->content) }}</div>
        @endif
    </article>
</div>

@endsection
