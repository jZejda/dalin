<div>
    <link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>


    <div id="map" style="width: 600px; height: 400px;"></div>
    <script>

        const map = L.map('map').setView([51.505, -0.09], 13);

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

        const obRaceSimple = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/ob-race-simple.png'});
        const obRaceDot = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/ob-race-dot.png'});
        const obRaceStages = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/ob-race-stages.png'});
        const trainingDot = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/training-dot.png'});

        @foreach($initialMarkers as $initialMarker)
            L.marker([{{$initialMarker['position']['lat']}}, {{$initialMarker['position']['lng']}}], {icon: {{$initialMarker['marker']}}}).addTo(map).bindPopup("adasdadd");
        @endforeach


        // const mGreen = L.marker([51.5, -0.09], {icon: greenIcon}).addTo(map);

        const popup = L.popup()
            .setLatLng([51.513, -0.09])
            .setContent('I am a standalone popup.')
            .openOn(map);

        function onMapClick(e) {
            popup
                .setLatLng(e.latlng)
                .setContent(`You clicked the map at ${e.latlng.toString()}`)
                .openOn(map);
        }

        map.on('click', onMapClick);

    </script>


</div>
