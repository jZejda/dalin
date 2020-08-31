{{-- \resources\views\frontend\event-result.blade.php --}}

@extends('layouts.frontend')

@section('title'){{ $page->title ?? '' }}@endsection

@section('content')


    <div class="border-t-2 bg-gray-200 text-gray-800">
        <div class="container mx-auto app-front-content bg-white px-4">

        <p>jede</p>

            <event-iofv3-result-component></event-iofv3-result-component>

        </div>
    </div>
@endsection
