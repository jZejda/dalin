{{-- \resources\views\admin\roles\index.blade.php --}}

@extends('layouts.admin')

@section('title', '| Legs')

@section('pageCustomCSS')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js" integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew==" crossorigin=""></script>

@endsection

@section('content')

    <!-- Content top header -->
    <div class="adm-main-header">

        <div class="flex justify-between">

            <div class="flex justify-start">
                <h1 class="adm-h1">Etapa - {{$oevent->title ?? ''}} </h1>
            </div>
        </div>
    </div>


    <!-- main content -->
    <div class="flex">
        <div class="w-1/2">

            <form class="w-full p-6" method="POST" action="{{ route('legs.store') }}">
            @csrf

                <!-- Name/DateTime -->
                <div class="flex">
                    <div class="w-1/2 px-1">

                        <label for="title" class="form-label">
                            {{ __('Jméno etapy') }}
                            @if ($errors->has('title'))
                                <span class="form-label-error">@component('admin.components.form-label-error-ico')@endcomponent{{ $errors->first('title') }}</span>
                            @endif
                        </label>
                        <input id="title" type="text" class="form-input-full {{ $errors->has('title') ? ' border-red-500 bg-white ' : '' }}" name="title" value="{{ old('title') }}">


                    </div>

                    <div class="w-1/2 px-1">

                        <label for="leg_datetime" class="form-label">
                            {{ __('Start Etapy') }}
                            @if ($errors->has('leg_datetime'))
                                <span class="form-label-error">
                                    @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('leg_datetime') }}
                                </span>
                            @endif
                        </label>
                        <date-time-input-component
                            :date_input_value="'{{ null !== old('leg_datetime') ? \Carbon\Carbon::createFromFormat('d.m.Y H:i:s', old('leg_datetime'))->subHour()->toISOString() : '' }}'"
                            :date_input_name="'leg_datetime'"
                        >
                        </date-time-input-component>
                        {{--
                        <input id="leg_datetime" placeholder="DD.MM.RRRR HH:MM:SS" type="text"
                               class="form-input-full {{ $errors->has('leg_datetime') ? ' form-input-error ' : '' }}"
                               name="leg_datetime" value="{{ old('leg_datetime') }}">
                               --}}
                    </div>

                </div>


                <!-- OrisId/RaceType -->
                <div class="flex">

                    <div class="w-1/2 px-1">

                        <label for="oris_id" class="form-label">
                            {{ __('Oris ID') }}
                            @if ($errors->has('oris_id'))
                                <span class="form-label-error">
                                    @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('oris_id') }}
                                </span>
                            @endif
                        </label>
                        <input id="leg_datetime" type="text"
                               class="form-input-full {{ $errors->has('oris_id') ? ' form-input-error ' : '' }}"
                               name="oris_id" value="{{ old('oris_id') }}">

                    </div>


                    <div class="w-1/2 px-1">

                        <label for="discipline_id" class="form-label">
                            {{ __('Disciplína') }}
                            @if ($errors->has('discipline_id'))
                                <span class="form-label-error">
                            @component('admin.components.form-label-error-ico')@endcomponent
                                    {{ $errors->first('discipline_id') }}
                    </span>
                            @endif
                        </label>
                        {{ Form::select('discipline_id', $discipline, null, array(
                            'name' => 'discipline_id',
                            'class' => $errors->has('discipline_id') ? ' form-input-full bg-white border-red-500' : ' form-input-full'
                        ))}}

                    </div>


                </div>


                <!-- Coorfinates lat/lon -->
                <div class="flex">
                    <div class="w-1/2 px-1">

                        <label for="lat" class="form-label">
                            {{ __('LAT') }}
                        @if ($errors->has('lat'))
                        <span class="form-label-error">@component('admin.components.form-label-error-ico')@endcomponent{{ $errors->first('lat') }}</span>
                        @endif
                        </label>
                        <input id="lat" type="text" class="form-input-full {{ $errors->has('lat') ? ' border-red-500 bg-white ' : '' }}" name="lat" value="{{ old('lat') }}">


                    </div>

                    <div class="w-1/2 px-1">

                        <label for="lon" class="form-label">
                            {{ __('LON') }}
                        @if ($errors->has('lon'))
                        <span class="form-label-error">@component('admin.components.form-label-error-ico')@endcomponent{{ $errors->first('lon') }}</span>
                        @endif
                        </label>
                        <input id="lon" type="text" class="form-input-full {{ $errors->has('lon') ? ' border-red-500 bg-white ' : '' }}" name="lon" value="{{ old('lon') }}">


                    </div>
                </div>

                <!-- Description -->
                <div class="flex">
                    <div class="w-full">

                        <label for="description" class="form-label">
                            {{ __('Popis etapy') }}
                            @if ($errors->has('description'))
                                <span class="form-label-error">@component('admin.components.form-label-error-ico')@endcomponent{{ $errors->first('description') }}</span>
                            @endif
                        </label>
                        <textarea rows="4" cols="50" id="description" type="text" class="form-input-full {{ $errors->has('description') ? ' border-red-500 bg-white ' : '' }}" name="description" value="{{ old('description') }}">{{ old('description') }}</textarea>

                    </div>
                </div>


                <div class="w-full px-1">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-700 text-gray-100 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        {{ __('Uložit Etapu') }}
                    </button>
                </div>

                {!! Form::hidden('oevent_id', Request::segment(4) ) !!}

            </form>

        </div>
        <!-- right content -->
        <div id="content-left-sidebar" class="w-1/2 border-l h-screen bg-gray-300">
            <div class="px-6 py-4 ">

                <!-- Item iterate -->
                <p>levypanel</p>

                @if(count($legs_in_event))

                    @foreach($legs_in_event as $leg_in_event)

                        <ul>
                            <li>{{$leg_in_event->leg_datetime}}</li>
                        </ul>

                        <div class="lg:flex shadow border  border-gray-400">
                            <div class="bg-blue-600 lg:w-2/12 py-4 block h-full shadow-inner">
                                <div class="text-center tracking-wide">
                                    <div class="text-white font-bold text-4xl ">24</div>
                                    <div class="text-white font-normal text-2xl">Sept</div>
                                </div>
                            </div>
                            <div class="w-full  lg:w-11/12 xl:w-full px-1 bg-white py-5 lg:px-2 lg:py-2 tracking-wide">
                                <div class="flex flex-row lg:justify-start justify-center">
                                    <div class="text-gray-700 font-medium text-sm text-center lg:text-left px-2">
                                        <i class="far fa-clock"></i> 1:30 PM
                                    </div>
                                    <div class="text-gray-700 font-medium text-sm text-center lg:text-left px-2">
                                        Organiser : IHC
                                    </div>
                                </div>
                                <div class="font-semibold text-gray-800 text-xl text-center lg:text-left px-2">
                                    {{ $leg_in_event->title }}
                                </div>

                                <div class="text-gray-600 font-medium text-sm pt-1 text-center lg:text-left px-2">
                                    {{ $leg_in_event->title }}
                                </div>
                            </div>
                            <div class="flex flex-row items-center w-full lg:w-1/3 bg-white lg:justify-end justify-center px-2 py-4 lg:px-0">
                                <span class="tracking-wider text-gray-600 bg-gray-200 px-2 text-sm rounded leading-loose mx-2 font-semibold">
                                  Going
                                </span>
                            </div>
                        </div>

                    @endforeach

                @endif


                <div id="mapid" style="width: 600px; height: 400px;"></div>
                <p>přesuň a zkopíruj kam je potřeba</p>

                <form>
                    <label for="latitude">Latitude:</label>
                    <input id="latitude" type="text" />
                    <label for="longitude">Longitude:</label>
                    <input id="longitude" type="text" />
                </form>



            </div>
        </div>
    </div>


@endsection

@section('pageCustomJS')

    <script>

        var mymap = L.map('mapid').setView([49.2065, 16.606], 13);

        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            maxZoom: 16,
            attribution: 'OSM',
        }).addTo(mymap);

        /*
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
            maxZoom: 18,
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
                '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
                'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(mymap);
        */

        /*
        var myMarker = L.marker([35.10418, -106.6287], {title: "MyPoint", alt: "The Big I", draggable: true})
            .addTo(mymap)
            .on('dragend', function() {
                var coord = String(myMarker.getLatLng()).split(',');
                console.log(coord);
                var lat = coord[0].split('(');
                console.log(lat);
                var lng = coord[1].split(')');
                console.log(lng);
                myMarker.bindPopup("Moved to: " + lat[1] + ", " + lng[0] + ".");
            });

         */

        var marker = L.marker([49.2065057835, 16.6064894199],{
            draggable: true
        }).addTo(mymap);

        marker.on('dragend', function (e) {
            document.getElementById('latitude').value = marker.getLatLng().lat;
            document.getElementById('longitude').value = marker.getLatLng().lng;
        });

    </script>

@endsection
