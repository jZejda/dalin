{{-- \resources\views\frontend\index.blade.php --}}

@extends('layouts.frontend')

@section('title'){{ $post->title }}@endsection

@section('content')

    <div class="border-t-2 bg-gray-200">
        <div class="container mx-auto bg-white px-6 my-6">
            <div class="html-content">

                <!-- News landing -->
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

        <div class="h-10 pb-6 bg-gray-300">
        </div>

    </div>
@endsection
