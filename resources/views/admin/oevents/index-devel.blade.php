{{-- \resources\views\admin\oevents\index-devel.blade.php --}}

@extends('layouts.admin')

@section('title', '| Akce')

@section('content')

    <oevent-list-component
        :events_in_year = "'{{ Request::segment(3) }}'"
        :events_from = "'{{ Request::segment(4) }}'"
        :is_canceled_show = 1
        :list_limit = 10
        >
    </oevent-list-component>


@endsection
