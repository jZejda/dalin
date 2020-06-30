{{-- \resources\views\admin\dashboard\index.blade.php --}}

@extends('layouts.admin')

@section('title', '| Úvodní stránka')

@section('content')


    <!-- Content -->
    <div class="flex-1 flex flex-col bg-gray-200">

        <div class="w-full">
            <!-- Content top header -->
        {{--
        <div class="px-6 py-2 items-center h-12">

            <div class="flex justify-between">

                <div class="flex justify-start">
                    <div class="text-center px-4 py-2 border-red-500 border-b-4 w-32">Novinky</div>
                    <div class="text-gray-600 text-center px-4 py-2 w-32">Závody</div>
                    <div class="text-gray-600 text-center px-4 py-2 w-32">Akce</div>
                </div>
                <div class="text-gray-700 text-center bg-gray-200 px-4 py-2">pravy</div>

            </div>
        </div>
            --}}

            <!-- Content description -->
            <div class="px-6 py-4 ">
                <h1 class="adm-h1">Úvodní stránka</h1>
            </div>

            <!-- Errors -->
            <div class="px-6">
                @include ('errors.list')
            </div>

            <!-- Content main part -->
            <div class="px-6 py-4 bg-gray-200">
                {{-- TODO if conten empty --}}
                <div class="container mx-auto html-content">
                    <div class="px-6 pb-2 bg-white">
                        @foreach ($last_posts as $post)

                            <div class="md:flex bg-white p-2 app-front-content">
                                    <div class="text-center md:text-left">
                                        <h2 class="text-lg">{{ $post->title }}</h2>
                                               <div class="text-gray-700 mb-2">{{ $post->editorial }}</div>
                                        <div class="text-gray-900 text-left">{!! $post->content !!}</div>
                                    </div>
                            </div>
                            <hr>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        @if (count($user_posts) != 0 || count($user_pages) != 0)
        @hasanyrole('Moderator|Super Admin')
        <div class="bg-yellow-400">
            <div class=" px-6 py-4 ">
            <h1 class="adm-h1 text-gray-800">Redaktor</h1>

                <div class="flex flex-col md:flex-row">
                    <div class="w-full md:w-1/2 flex">
                        <div class="w-full p-4 rounded-lg bg-white mb-2 md:mr-2 md:mb-0">
                            <h2 class="text-gray-800">Novinky</h2>
                            @if(count($user_posts) != 0)
                                @foreach($user_posts as $user_post)
                                    <div class="flex justify-between">
                                        <div>
                                            <span class="text-gray-600 font-hairline tracking-tighter">{{ $user_post->updated_at->format('d.m.Y - H:i') }} - </span>
                                            <span class="font-bold">{{ $user_post->title }}</span>
                                        </div>
                                        <div class="flex">
                                            <a href="{{ route('posts.show', $user_post->id ) }}" class="adm-act-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </a>
                                            <a href="{{ route('posts.edit', $user_post->id ) }}" class="adm-act-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3">
                                                    <path d="M12 20h9"></path>
                                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                                </svg>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-bold">Zatím jsi nevytvořil žádnou novinku</p>
                            @endif
                        </div>
                    </div>
                    <div class="w-full md:w-1/2 flex">
                    <div class="w-full p-4 rounded-lg bg-white md:ml-2">
                        <h2 class="text-gray-800">Články</h2>
                        @if(count($user_pages) != 0)
                            @foreach($user_pages as $user_page)
                                <div class="flex justify-between">
                                    <div>
                                        <span class="text-gray-600 font-hairline tracking-tighter">{{ $user_page->updated_at->format('d.m.Y - H:i') }} - </span>
                                        <span class="font-bold">{{ $user_page->title }}</span>
                                    </div>
                                    <div class="flex">
                                        <a href="{{ route('pages.show', $user_page->id ) }}" class="adm-act-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                        <a href="{{ route('pages.edit', $user_page->id ) }}" class="adm-act-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p>Zatím jsi nevytvořil žádný článek</p>
                        @endif
                    </div>
                </div>
                </div>
            </div>
        </div>
        @endhasanyrole
        @endif
    </div>

@endsection
