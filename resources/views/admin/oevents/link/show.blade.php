{{-- \resources\views\admin\oevents\index.blade.php --}}

@extends('layouts.admin')

@section('title', '| Akce')


@section('content')

    <!-- Top Content bar -->

    @if(Session::has('flash_message'))
        <toasted-component :message="'{!! html_entity_decode(Session::get('flash_message')) !!}'"
                           :type="'success'"></toasted-component>
    @endif

    <!-- Content top header -->
    <div class="adm-main-header">

        <div class="flex justify-between">

            <div class="flex justify-start">
                <h1 class="adm-h1">Linky</h1>
            </div>
            <div class="flex justify-start">
                <a href="" title="Link"
                   class="btn-ico btn-ico-blue">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-compass" width="24"
                         height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z"/>
                        <polyline points="8 16 10 10 16 8 14 14 8 16"/>
                        <circle cx="12" cy="12" r="9"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>


    <!-- main content -->
    <div class="flex">
        <!-- Oevent Info -->

        <!-- Legs Info -->

    </div>

@endsection
