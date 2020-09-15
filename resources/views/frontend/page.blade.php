{{-- \resources\views\frontend\page.blade.php --}}

@extends('layouts.frontend')

@section('title'){{ $page->title }}@endsection

@section('content')


    <div class="border-t-2 bg-gray-200 text-gray-800">
        <div class="container mx-auto app-front-content bg-white px-4">

            @if (isset($categoryName)) {{-- if content cat menu set --}}

            <!-- Two columns 1 - 3/4 -->
            <div class="flex md:flex-row flex-wrap">
                <div class="w-full md:w-3/4 pb-4">
                    <div class="html-content">
                        <h1>{{$page->title}}</h1>
                        <div class="border-2 border-yellow-600 mb-6" style="max-width: 50px;"></div>
                        <div>
                            {!! $page->content !!}
                        </div>
                    </div>
                </div>

                <!-- Two columns 2 - 1/4 -- -->
                <div class="w-full md:w-1/4 md:pl-4 md:pt-2">
                    <div id="content-left-sidebar">

                        <div class="font-sans container bg-gray-200 mx-auto shadow bg-cover border-b border-white"
                             style="color:#606F7B;background-color: rgb(165, 182, 198); background-image:url('https://images.unsplash.com/photo-1529410310965-7fecf505f103?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=634&q=80');">
                            <div class="px-4 pt-2 pb-0">
                                <h2 class="text-white text-2xl">{{$categoryName->title}}</h2>
                                <p class="text-gray-400">{{$categoryName->description}}</p>
                            </div>
                        </div>
                        @foreach ($pagesMenu as $pageItem)
                            <div class="bg-green-600 px-6 py-2 text-white border-b border-white">
                                <a href="/stranka/{{ $pageItem->slug }}"

                                   class="{{ Request::is('stranka/{$pageItem->slug}') ? 'text-gray-700' : '' }} font-medium hover:underline mt-6 py-2 pt-2">{{ $pageItem->title }}</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>


            @else

            <div class="flex-1 flex flex-row">
                <div class="w-full">
                    <div class="px-6 py-4">
                        <div class="html-content">
                            <h1>{{$page->title}}</h1>
                            <div class="border-2 border-yellow-600 mb-6" style="max-width: 50px;"></div>
                            <div>
                                {!! $page->content !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @endif

        </div>
    </div>
@endsection
