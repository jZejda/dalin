{{-- \resources\views\admin\oevents\index.blade.php --}}

@extends('layouts.admin')

@section('title', '| Akce')

<style>

    @import url('https://fonts.googleapis.com/css?family=Oswald&display=swap');

    .my_text {
        font-family: 'Oswald', sans-serif;
        font-weight:    500;
    }

</style>

@section('content')

    <!-- Top Content bar -->

    @if(Session::has('flash_message'))
        <toasted-component :message="'{!! html_entity_decode(Session::get('flash_message')) !!}'" :type="'success'"></toasted-component>
    @endif

        <!-- Content top header -->
        <div class="adm-main-header">

            <div class="flex justify-between">

                <div class="flex justify-start">
                    <h1 class="adm-h1">Akce - {{ $oevent->title }}</h1>
                </div>
                <div class="flex justify-start">
                    <a href="{{ URL::route('legs.create') }}/{{ $oevent->id }}" title="Přidej etapu" class="btn-ico btn-ico-blue">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-compass" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z"/>
                            <polyline points="8 16 10 10 16 8 14 14 8 16" />
                            <circle cx="12" cy="12" r="9" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>


        <!-- main content -->
        <div class="flex">
            <!-- Oevent Info -->
            <div class="w-1/4">
                <div class="px-4 py-4">
                    <h1>{{ $oevent->title }}</h1>
                    <ul>
                        <li>{{ $oevent->place }}</li>
                        <li>{{ $oevent->place }}</li>
                        <li>{{ $oevent->from_date }}</li>
                        <li>{{ $oevent->to_date }}</li>
                    </ul>

                    <hr>
                    <h3>Etapy</h3>

                    @if(count($legs) > 0)

                        @foreach($legs as $leg)
                            <p>{{ $leg->title }}</p>
                            <p>{{ $leg->leg_datetime }}</p>
                            <p>{{ $leg->lat }}</p>
                            <p>{{ $leg->lon }}</p>

                            <hr>
                        @endforeach
                    @else
                        <p>Ukazuje tlačítko na přídání etapy</p>
                        <a href="{{ URL::route('legs.create') }}/{{ $oevent->id }}" title="Přidej etapu" class="btn-ico btn-ico-blue">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-compass" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                <path stroke="none" d="M0 0h24v24H0z"/>
                                <polyline points="8 16 10 10 16 8 14 14 8 16" />
                                <circle cx="12" cy="12" r="9" />
                            </svg>
                        </a>
                    @endif




                </div>

            </div>
            <!-- Legs Info -->
            <div class="w-3/4">
                <div class="px-4 py-4">
                    <p>fsdfsdf</p>
                </div>
            </div>
        </div>

@endsection
