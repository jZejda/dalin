{{-- \resources\views\admin\posts\show.blade.php --}}

@extends('layouts.admin')

@section('title', '| Novinka')

@section('content')

@if(Session::has('flash_message'))
  <toasted-component :message="'{!! html_entity_decode(Session::get('flash_message')) !!}'" :type="'success'"></toasted-component>
@endif

<!-- Content -->
<div class="flex-1 flex flex-row bg-gray-200">

    <div class="w-full">
        <!-- Content top header -->
        <div class="adm-main-header">

            <div class="flex justify-between">

                <div class="flex justify-start">
                    <h1 class="adm-h1">Náhled novinky</h1>
                </div>
                <div class="flex justify-start">
                    <a href="{{ route ('posts.index')}}/{{ $post->id }}/edit" title="Uprav novinku" class="btn-ico btn-ico-blue">
                        <svg class="btn-ico-fater" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                        </svg>
                    </a>
                    <a href="{{ route('post')}}" title="Zpět na seznam" class="btn-ico btn-ico-blue ml-2">
                        <svg class="btn-ico-fater" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="8" y1="6" x2="21" y2="6"></line>
                            <line x1="8" y1="12" x2="21" y2="12"></line>
                            <line x1="8" y1="18" x2="21" y2="18"></line>
                            <line x1="3" y1="6" x2="3" y2="6"></line>
                            <line x1="3" y1="12" x2="3" y2="12"></line>
                            <line x1="3" y1="18" x2="3" y2="18"></line>
                        </svg>
                    </a>

                        <delete-content-modal-component
                                :del_content_model="'{{ 'posts' }}'"
                                :del_content_name="'{{ $post->title }}'"
                                :del_content_id="'{{ $post->id }}'"
                                :del_is_button="'{{ 'true' }}'"
                                :del_content_czname="'novinku'"
                        >
                        </delete-content-modal-component>

                </div>
            </div>
        </div>

        <!-- Content main part -->
        <div class="mx-auto mb-4">
            <div class="bg-yellow-500 text-center  py-2">
                --- náhled novinky na titulní stránce ---
            </div>
        </div>
        <div class="container mx-auto">

            <div class="px-6 py-4 bg-white">

                @component('app-components.news_short')
                    @slot('img_url'){{ $post->img_url }}@endslot
                    @slot('post_id'){{ $post->id }}@endslot
                    @slot('post_title'){{ $post->title }}@endslot
                    @slot('post_editorial'){{ $post->editorial }}@endslot
                    @slot('post_user_color'){{ $post_user->color }}@endslot
                    @slot('post_user_name'){{ $post_user->name }}@endslot
                    @slot('post_created_at'){{ $post->created_at }}@endslot
                @endcomponent

            </div>
        </div>

        <div class="mx-auto my-4">
            <div class="bg-yellow-500 text-center  py-2">
                --- plný náhled novinky ---
            </div>
        </div>

        <div class="container mx-auto">

            <div class="px-6 py-4 bg-white">
                <div class="html-content">
                    <!-- News full -->
                    @component('app-components.news_long')
                        @slot('img_url'){{ $post->img_url }}@endslot
                        @slot('post_id'){{ $post->id }}@endslot
                        @slot('post_title'){{ $post->title }}@endslot
                        @slot('post_editorial'){{ $post->editorial }}@endslot
                        @slot('post_content'){!! $post->content !!}@endslot
                        @slot('post_created_at'){{ $post->created_at }}@endslot
                    @endcomponent
                </div>

            </div>
        </div>

        <div class="mx-auto my-4">
            <div class="bg-gray-300 h-6"></div>
        </div>


    </div>
</div>

@endsection
