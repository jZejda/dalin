@php
    /** @var array $mapData */
@endphp

<div>
    <link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>


    <div id="map" style="width: 100%; height: 400px;"></div>
    <script>

        const map = L.map('map').setView([{{$mapData['centerMap']['lat']}}, {{$mapData['centerMap']['lon']}}], 13);

        const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        const LeafIcon = L.Icon.extend({
            options: {
                shadowUrl: '{{ URL::to('/') }}/images/markers/marker-shadow.png',
                iconSize:     [32, 36],
                shadowSize:   [41, 41],
                iconAnchor:   [16, 34],
                shadowAnchor: [12, 41],
                popupAnchor:  [-3, -38]
            }
        });

        const raceSimple = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/ob-race-simple.png'});
        const raceDot = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/ob-race-dot.png'});
        const raceStages = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/ob-race-stages.png'});
        const trainingDot = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/training-dot.png'});

        const stageStart = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/training-dot.png'});
        const defaultMarker = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/training-dot.png'});

        @foreach($mapData['markers'] as $marker)
            {{--Importatnt to take space--}}
            L.marker([{{$marker->lat}}, {{$marker->lng}}], {icon: {{$marker->markerType->value}}}).addTo(map).bindPopup("<strong>{{$marker->label}}</strong><br />{{$marker->popupContent}}");

        @endforeach

    </script>
</div>

