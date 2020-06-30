{{-- \resources\views\admin\oevents\index-devel.blade.php --}}

@extends('layouts.admin')

@section('title', '| Akce')

@section('content')

    <oevent-list-component
        :events_in_year = "'{{ Request::segment(3) }}'"
        >
    </oevent-list-component>


@endsection
