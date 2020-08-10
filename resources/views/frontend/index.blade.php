{{-- \resources\views\frontend\index.blade.php --}}

@extends('layouts.frontend')

@section('title', 'Úvod')

@section('pageCustomCSS')
    <link rel="stylesheet" href="{{ asset ("vendor/pe-icon-set-weather/css/pe-icon-set-weather.css") }}">
    <link rel="stylesheet" href="{{ asset ("vendor/pe-icon-set-weather/css/helper.css") }}">
@endsection

<style>

    @import url('https://fonts.googleapis.com/css?family=Oswald&display=swap');

    .my_text {
        font-family: 'Oswald', sans-serif;
        font-weight:    500;
    }

</style>

@section('content')

    <!-- Top Content bar -->
    @if(isset($oevents))
        <!-- event block -->
        <div class="bg-topo-on-white">
            <div class="pb-3 pt-1 flex flex-wrap justify-center">
                @foreach ($oevents as $oevent)

                    @if($oevent->event_category == 1)
                        @php($sport_collor = 'bg-green-500')
                    @elseif($oevent->event_category == 2)
                        @php($sport_collor = 'bg-orange-500')
                    @elseif($oevent->event_category == 3)
                        @php($sport_collor = 'bg-blue-500')
                    @elseif($oevent->event_category == 4)
                        @php($sport_collor = 'bg-purple-500')
                    @else
                        @php($sport_collor = 'bg-gray-500')
                    @endif

                    <div class="relative m-1 max-w-xs w-full sm:w-full lg:max-w-xs shadow-lg my_text w-x overflow-hidden">
                        @if($oevent->is_canceled)
                        <div class="oe-ribbon-corner bg-red-500 text-sm whitespace-no-wrap px-12">ZRUŠENO</div>
                        @endif
                        <div class="text-white px-4 py-1 {{$sport_collor}}">
                            <span class="block font-semibold text-2xl">{{ Str::limit($oevent->title, 60, '...') }}</span>

                            <div class="flex justify-between">

                                <div class="block">
                                    @if(isset($oevent->to_date))
                                        <span class="font-bold">{{\Carbon\Carbon::parse($oevent->from_date)->format('d -') }}</span>
                                        <span class="font-bold">{{\Carbon\Carbon::parse($oevent->to_date)->format('d.m.Y') }}</span>
                                    @else
                                        <span class="font-bold">{{\Carbon\Carbon::parse($oevent->from_date)->format('d.m.Y') }}</span>
                                    @endif
                                </div>
                                <div class="block">
                                    <span class="block font-semibold">{{ Str::limit($oevent->place, 30) }}</span>
                                </div>

                            </div>


                        </div>
                        <div class="bg-white text-black px-4 py-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">POŘADATEL</span>
                                <span class="text-sm text-gray-600">OBLAST</span>
                            </div>
                            <div class="flex justify-between">
                                @php($clubs = $oevent->clubs)
                                <span class="font-bold">
                                @foreach($clubs as $club)
                                            {{ $club }}
                                @endforeach

                        </span>
                                <span class="font-bold">
                        @php($regions_in_event = $oevent->regions)

                                    @foreach ($regions_in_event as $actual)
                                        {{ $regions[$actual] }}
                                    @endforeach
                        </span>
                            </div>
                        </div>
                        {{-- Dates --}}
                        <div class="border-t-2 border-gray-200 bg-white text-black px-4 py-2 tracking-tight">
                            <div class="block text-sm text-gray-600">PŘIHLÁŠKY</div>

                            @if(isset($oevent->first_date))
                                <span class="mr-4 text-lg">{{\Carbon\Carbon::parse($oevent->first_date)->format('d.m.Y') }}<span class="text-sm text-gray-500"> |1</span></span>
                            @endif
                            @if(isset($oevent->second_date))
                                <span class="mr-4 text-lg">{{\Carbon\Carbon::parse($oevent->second_date)->format('d.m.Y') }}<span class="text-sm text-gray-500"> |2</span></span>
                            @endif
                            @if(isset($oevent->third_date))
                                <span  class="mr-4 text-lg">{{\Carbon\Carbon::parse($oevent->second_third)->format('d.m.Y') }}<span class="text-sm text-gray-500"> |3</span></span>
                            @endif
                        </div>

                        @if (count($oevent->legs))
                            @foreach($oevent->legs as $leg)
                                {{-- Legs --}}
                                <div class="bg-gray-200 text-black px-4 py-2 {{ !$loop->last ? 'border-dotted border-b-4 border-gray-400' : '' }}">

                                    @if ($loop->first AND count($oevent->legs) > 1)
                                        <span class="block text-gray-700 text-sm font-bold">ETAPY</span>
                                    @endif

                                    {{-- Leg Name Place --}}

                                    <div class="block text-xl -my-1">
                                        {{ (count($oevent->legs) > 1) ? $leg->title.'-' : '' }} {{ $disciplines[$leg->discipline_id] }}
                                    </div>

                                    <div class="flex justify-between">
                                        <div class="flex flex-row items-center -my-1">
                                            <div class="text-4xl">
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
                                            </div>
                                            <div class="flex flex-col ml-4">
                                                <div class="-my-1">{{\Carbon\Carbon::parse($leg->leg_datetime)->format('d.m.') }}</div>
                                                <div class="-my-1">{{\Carbon\Carbon::parse($leg->leg_datetime)->format('H:i') }}</div>
                                            </div>
                                            <div class="ml-4 text-gray-600">
                                                <a href="{{ 'https://mapy.cz/turisticka?x='.$leg->lon.'&y='.$leg->lat.'&z=14&source=coor&id='.$leg->lon.'%2C'.$leg->lat.'&q='.$leg->lat.'N%20'.$leg->lon.'E' }}" target="_blank">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-map-pin"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                                                </a>
                                            </div>
                                        </div>
                                        {{-- Forecast --}}
                                        <div class="flex flex-row items-center mr-2">

                                            @if(isset($leg->forecast))

                                                @php($forecast_data = json_decode($leg->forecast))

                                                <span class="text-gray-800">
                                                    @component('admin.components.forecast-ico-new')
                                                        @slot('icon'){{ $forecast_data->icon }}@endslot
                                                            @slot('ico_size'){{32}}@endslot
                                                        @slot('summary'){{ $forecast_data->summary }}@endslot
                                                    @endcomponent
                                                </span>
                                                <span class="ml-2 w-12 text-gray-800 tracking-tighter text-4xl">{{ round($forecast_data->temperature, 1) }}°</span>

                                            @else
                                                <span class="text-gray-400">
                                                    @component('admin.components.forecast-ico')
                                                        @slot('icon'){{ 'none' }}@endslot
                                                        @slot('ico_size'){{32}}@endslot
                                                        @slot('summary'){{ 'N/A' }}@endslot
                                                    @endcomponent
                                                </span>
                                                <span class="ml-2 w-12 text-gray-400 tracking-tighter text-4xl">---°</span>

                                            @endif

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- News Block -->
    <div class="border-t-2 bg-gray-200">
        <div class="container mx-auto">
            <!-- News landing -->
            <div class="flex justify-between my-3">
                <div class="tracking-tight text-3xl text-gray-800 text-center">
                    <span class="font-bold">NOVINKY</span>
                    <span class="font-thin">{{ now()->year }}</span>
                </div>
                <div class="text-gray-800 text-center mt-2 underline">
                    <a href="{{ url('novinky/vse') }}"> ARCHIV NOVINEK</a>
                </div>
            </div>
            <!-- End of News landing -->

            <!-- Two columns 1 - 3/4 -->
            <div class="flex md:flex-row flex-wrap">
                <div class="w-full md:w-3/4 pb-4">
                    @foreach ($posts as $post)

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

                <!-- Two columns 2 - 1/4 -- -->
                <div class="w-full md:w-1/4 md:pl-4 md:pt-2">

                    <div class="shadow-md">
                        <div class="font-sans container bg-gray-200 mx-auto pb-4 bg-cover rounded-t-sm"
                             style="color:#606F7B;background-color: rgb(165, 182, 198); background-image:url('images/app-default/object-background/training-01-default.jpg');">
                            <div class="px-6 pt-4">
                                <div class="font-bold text-mb-2 text-gray-100 tracking-tight font-thin">
                                    <span>TRÉNINK</span>
                                </div>
                            </div>
                            <div class="px-6 py-1">
                                <div class="font-sans">
                                    <div class="text-gray-100">Aktualizovaný seznam pravidelných tréninků oblasti.</div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-500 px-6 py-2 text-center mb-2 text-white rounded-b-sm">
                            <a href="{{ url('https://docs.google.com/spreadsheets/d/1TwlGXXti_UMW_iAVwF5jaiF7EAeVyJgrfaOhFFvWVRU/edit#gid=0 ') }}" target="_blank" class="no-underline hover:underline text-white">GOOGLE DOCS</a>
                        </div>
                    </div>

                    <div class="shadow-md">
                        <div class="font-sans container bg-gray-200 mx-auto pb-4 bg-cover rounded-t-sm"
                             style="color:#606F7B;background-color: rgb(165, 182, 198); background-image:url('images/app-default/object-background/forest-01.jpeg');">
                            <div class="px-6 py-4">
                                <div class="font-bold text-mb-2 text-gray-300 tracking-tight font-thin">
                                    <span>ALFÍ TÝDEN</span>
                                </div>
                            </div>
                            <div class="px-6 py-1">
                                <div class="text-gray-500 font-sans">
                                    <div class="text-gray-200">Průběžné informace nadcházejících akcí.</div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-500 px-6 py-2 text-center mb-2 text-white">
                            <a href="{{ url('stranka/alfi-tyden') }}" class="no-underline hover:underline text-white">WEB</a>
                        </div>
                    </div>

                    <!--
                    <div class="shadow-md">
                        <div class="font-sans container bg-gray-200 mx-auto pb-4 bg-cover"
                             style="color:#606F7B;background-color: rgb(165, 182, 198); background-image:url('images/app-default/object-background/bzl-logo.jpeg');">
                            <div class="px-6 py-4">
                                <div class="font-bold text-mb-2 text-gray-400 tracking-tight font-thin">
                                    <span class="rounded-full bg-blue-600 h-4 w-4 flex items-center justify-center mb-2"></span>
                                    <span>Brněnská zimní liga</span>
                                </div>
                                <h2 class="text-white mt-2"></h2>
                            </div>
                            <div class="px-6 py-4">
                                <div class="text-gray-300 font-sans">
                                    <div>2019/2020</div>
                                    <span>Běháme i v zimě, běhejte taky.</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-500 px-6 py-4 text-center mb-4 text-white">
                            <a href="{{ url('https://bzl.zabiny.club/') }}" target="_blank" class="no-underline hover:underline text-white">BZL WEB</a>                            </div>
                    </div>
                    -->
                    <div class="shadow-md">
                        <div class="font-sans container bg-gray-200 mx-auto pb-4 bg-cover rounded-t-sm"
                             style="color:#606F7B;background-color: rgb(165, 182, 198); background-image:url('images/app-default/object-background/forest-path-01.jpeg');">
                            <div class="px-6 pt-5">
                                <div class="font-bold text-mb-2 text-gray-400 tracking-tight font-thin">
                                    <span>POŘÁDÁME</span>
                                </div>
                                <h2 class="text-white mt-2">10 JmL Kobeřice u Brna</h2>
                            </div>
                            <div class="px-6 py-4">
                                <div class="text-gray-500 font-sans">
                                    <div>10.10.2020</div>
                                    <span>Kobeřice hřiště</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-500 px-6 py-2 text-center mb-4 text-white">
                            <a href="{{ url('stranka/10-jml-2020-koberice') }}" class="no-underline hover:underline text-white">WEB</a> |
                            <a href="https://oris.orientacnisporty.cz/Zavod?id=6052" target="_blank" class="no-underline hover:underline text-white">ORIS</a>
                        </div>
                    </div>

                    <!--
                    <div class="shadow-md">
                        <div class="font-sans container bg-gray-200 mx-auto pb-4 bg-cover rounded-t-sm"
                             style="color:#606F7B;background-color: rgb(165, 182, 198); background-image:url('images/app-default/object-background/forest-path-01.jpeg');">
                            <div class="px-6 pt-5">
                                <div class="font-bold text-mb-2 text-gray-400 tracking-tight font-thin">
                                    <span>POŘÁDÁME</span>
                                </div>
                                <h2 class="text-white mt-2">JML Kobeřice u Brna</h2>
                            </div>
                            <div class="px-6 py-4">
                                <div class="text-gray-500 font-sans">
                                    <div>17.10.2020</div>
                                    <span>Kobeřice u Brna</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-500 px-6 py-2 text-center mb-4 text-white">
                            <a href="{{ url('stranka/10-jml-2020-koberice') }}" class="no-underline hover:underline text-white">WEB</a> |
                            <a href="https://oris.orientacnisporty.cz/Zavod?id=5663" target="_blank" class="no-underline hover:underline text-white">ORIS</a>
                        </div>
                    </div>
                    -->

                </div>
            </div>
        </div>
    </div>
@endsection
