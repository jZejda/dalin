{{-- \resources\views\frontend\postsAll.blade.php --}}

@extends('layouts.frontend')

@section('title', 'Novinky vše')

@section('content')

    <div class="border-t-2 bg-gray-200">
        <div class="container mx-auto bg-white px-6 my-6 pt-6">
            <div class="html-content">

                @foreach ($posts as $post)
                    <!-- News landing -->
                        @component('app-components.news_short')
                            @slot('img_url'){{ $post->img_url }}@endslot
                            @slot('post_id'){{ $post->id }}@endslot
                            @slot('post_title'){{ $post->title }}@endslot
                            @slot('post_editorial'){{ $post->editorial }}@endslot
                            @slot('post_user_color'){{ $post->user_color }}@endslot
                            @slot('post_user_name'){{ $post->user_name }}@endslot
                            @slot('post_created_at'){{ $post->created_at }}@endslot
                        @endcomponent
                @endforeach

            </div>

            <div class="pb-6">
                {{ $posts->links() }}
            </div>

        </div>

        <div class="h-10 pb-6 bg-gray-300"></div>


    </div>
@endsection
