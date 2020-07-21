{{-- \resources\views\admin\oevents\index.blade.php --}}

@extends('layouts.admin')

@section('title', '| Akce')

@section('pageCustomCSS')
    <link rel="stylesheet" href="{{ asset ("vendor/pe-icon-set-weather/css/pe-icon-set-weather.css") }}">
    <link rel="stylesheet" href="{{ asset ("vendor/pe-icon-set-weather/css/helper.css") }}">

    {{--
    http://www.pixeden.com/icon-fonts/the-icons-font-set-weather
    http://themes-pixeden.com/font-demos/the-icons-set/weather/documentation.html
    http://themes-pixeden.com/font-demos/the-icons-set/weather/

    --}}
@endsection

<style>



    @import url('https://fonts.googleapis.com/css?family=Oswald&display=swap');

    .my_oswald {
        font-family: 'Oswald', sans-serif;
        font-weight:    500;
    }

</style>

@section('content')

    <!-- Top Content bar -->

    @if(Session::has('flash_message'))
        <toasted-component :message="'{!! html_entity_decode(Session::get('flash_message')) !!}'" :type="'success'"></toasted-component>
    @endif

    @if (count($oevents) === 0 )

        @component('admin.components.newitem')
            @slot('btnlabel')
                vytvoř akci
            @endslot
            @slot('btnurl')
                admin/oevents/create
            @endslot
        @endcomponent

    @elseif (count($oevents) >= 1)

        <!-- Content top header -->
        <div class="adm-main-header">

            <div class="flex justify-between">

                <div class="flex justify-start">
                    <h1 class="adm-h1">Akce</h1>
                </div>
                <div class="flex justify-start">
                    @can('Create Oevents')
                    <a href="{{ URL::to('admin/oevents/create') }}" title="Přidej akci" class="btn-ico btn-ico-blue">
                        <svg class="btn-ico-fater" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        <!-- Errors -->
        <div class="px-6">

            @include ('errors.list')

        </div>

        <div class="bg-gray-200 p-2 my_oswald">

            @foreach ($oevents as $oevent)

                @if($oevent->event_category == 1)
                    @php($sport_collor = 'border-green-500')
                @elseif($oevent->event_category == 2)
                    @php($sport_collor = 'border-orange-500')
                @elseif($oevent->event_category == 3)
                    @php($sport_collor = 'border-blue-500')
                @elseif($oevent->event_category == 4)
                    @php($sport_collor = 'border-purple-500')
                @else
                    @php($sport_collor = 'border-gray-500')
                @endif


                <div class="grid grid-rows-1 grid-cols-12 mb-2">
                    <div class="p-2 col-span-1 bg-white h-full rounded-l border-l-8 {{$sport_collor}} {{ !$loop->last ? 'mb-1' : '' }}">
                        <div class="flex flex-col justify-center">
                            <div class="block">
                                @if(isset($oevent->to_date))
                                    <span class="font-bold">{{\Carbon\Carbon::parse($oevent->from_date)->format('d -') }}</span>
                                    <span class="font-bold">{{\Carbon\Carbon::parse($oevent->to_date)->format('d.m.Y') }}</span>
                                @else
                                    <span class="font-bold">{{\Carbon\Carbon::parse($oevent->from_date)->format('d.m.Y') }}</span>
                                @endif
                            </div>
                            <div>
                                {{ $sports[$oevent->sport_id] }}<br>
                                <span class="uppercase">{{ $event_category[$oevent->event_category] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-span-5 bg-white h-full border-l-2 lg:border-dotted border-gray-200">
                        <div class="flex flex-col p-1 pl-2">
                            <div>
                                <span class="font-semibold text-2xl">{{ $oevent->title }} </span><span class="text-2xl font-hairline"> | {{ $oevent->place }}</span>
                            </div>
                            <div class="text-gray-300">

                                {{-- Regions --}}
                                @php($regions_in_event = $oevent->regions)

                                @foreach ($regions_in_event as $actual)
                                    <span class="text-gray-800">{{ $regions[$actual]}}</span>
                                @endforeach

                                {{-- Clubs --}}
                                @php($clubs = $oevent->clubs)
                                <span class="font-bold">
                                    @foreach($clubs as $club)
                                        <spna class="text-gray-800">{{ $club }}</spna>
                                    @endforeach

                                @if($oevent->oris_id == null)
                                    <span class="text-sm">ORIS</span>
                                @else
                                    <span class="text-sm text-gray-800">ORIS</span>
                                @endif
                                <span> | </span>
                                @if($oevent->url == null)
                                    <span class="text-sm">STRÁNKY ZÁVODU</span>
                                @else
                                    <span class="text-sm text-gray-800">STRÁNKY ZÁVODU</span>
                                @endif
                            </div>
                            {{-- Termíny přihlášek --}}
                            <div class="text-gray-800">
                                @if($oevent->first_date != null)
                                    <span class="text-xs">{{\Carbon\Carbon::parse($oevent->first_date)->format('d.m.Y H:i') }}</span>
                                @endif
                                @if($oevent->second_date != null)
                                    <span class="ml-1 text-xs">{{\Carbon\Carbon::parse($oevent->second_date)->format('d.m.Y H:i') }}</span>
                                @endif
                                @if($oevent->third_date != null)
                                    <span class="ml-1 text-xs">{{\Carbon\Carbon::parse($oevent->third_date)->format('d.m.Y H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    {{-- Legs--}}
                    <div class="col-span-5">
                        <div class="flex flex-col bg-white h-full border-l-2 border-dotted border-gray-300">
                            <div class="flex-1 p-1 pl-2">
                                @if (count($oevent->legs))
                                    @if(count($oevent->legs) > 2)
                                        @php($leg_names = true)
                                    @else
                                        @php($leg_names = false)
                                    @endif
                                    @foreach($oevent->legs as $leg)

                                        <div class="flex justify-between">
                                            <div class="flex flex-row items-center ">
                                                <div class="text-xl text-gray-600 tracking-tighter">
                                                    @php ($day = \Carbon\Carbon::parse($leg->leg_datetime)->format('w') )
                                                    @if($day == 1)
                                                        Po
                                                    @elseif($day == 2)
                                                        Út
                                                    @elseif($day == 3)
                                                        St
                                                    @elseif($day == 4)
                                                        Čt
                                                    @elseif($day == 5)
                                                        Pá
                                                    @elseif($day == 6)
                                                        So
                                                    @else
                                                        Ne
                                                    @endif
                                                    {{\Carbon\Carbon::parse($leg->leg_datetime)->format('d.') }}
                                                    {{\Carbon\Carbon::parse($leg->leg_datetime)->format('H:i') }}
                                                </div>
                                                {{--
                                                <div class="flex flex-col ml-2 text-xs text-gray-600">
                                                    <div class="-my-1 pb-1">{{\Carbon\Carbon::parse($leg->leg_datetime)->format('d.m') }}</div>
                                                    <div class="-my-1">{{\Carbon\Carbon::parse($leg->leg_datetime)->format('H:i') }}</div>
                                                </div>
                                                --}}
                                                <div class="text-xl ml-2">
                                                    @if($leg_names)
                                                        <span class="text-gray-600 font-hairline">{{ $leg->title }}</span>
                                                    @endif
                                                        <span>{{ $disciplines[$leg->discipline_id] }}</span>


                                                </div>
                                                <div class="ml-4 text-gray-600">
                                                    <a href="{{ 'https://mapy.cz/turisticka?x='.$leg->lon.'&y='.$leg->lat.'&z=14&source=coor&id='.$leg->lon.'%2C'.$leg->lat.'&q='.$leg->lat.'N%20'.$leg->lon.'E' }}" target="_blank">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>


                                    @endforeach
                                @endif


                            </div>
                        </div>
                    </div>
                    <div class="col-span-1 bg-white h-full rounded-r">
                        <div class="flex justify-end h-full">
                            <div class="pr-2">
                                <div class="flex flex-col items-center justify-center h-full">
                                    @can('Show Oevents')
                                        <a href="{{ route('oevents.show', $oevent->id ) }}" class="adm-act-btn pb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('Edit Oevents')
                                        <a href="{{ route('oevents.edit', $oevent->id ) }}" class="adm-act-btn pb-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-3">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path>
                                            </svg>
                                        </a>
                                    @endcan
                                    @can('Delete Oevents')
                                        <delete-content-modal-component
                                            :del_content_model="'{{ 'oevents' }}'"
                                            :del_content_name="'{{ $oevent->title }}'"
                                            :del_content_id="'{{ $oevent->id }}'"
                                            :del_content_czname="'akci'"
                                        >
                                        </delete-content-modal-component>
                                    @endcan
                                </div>

                            </div>
                            <div class="pr-2 bg-yellow-400 rounded-r">
                                <div class="flex items-center justify-end h-full">
                                    <div class="ml-2">></div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div>

            @endforeach

            @else
                <p>zadny vystup</p>
            @endif

        </div>

@endsection
