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
                    <a href="{{ URL::route('legs.create') }}/{{ $oevent->id }}" title="Přidej akci" class="btn-ico btn-ico-blue">
                        <svg class="btn-ico-fater" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <div class="px-4 py-4 mt-8">

            <h3>Etapy</h3>

            @if(count($legs) > 0)

                <p>ukazuje etapy</p>
                @foreach($legs as $leg)
                    <p>{{ $leg->title }}</p>
                @endforeach
            @else
                <p>Ukazuje tlačítko na přídání etapy</p>
            @endif

            <div class="w-full px-1">
                <a href="{{route ('legs.create',['oevent_id' => $oevent->id])}}" class="btn btn-blue-outline">Zpět</a>
            </div>



        </div>
@endsection
