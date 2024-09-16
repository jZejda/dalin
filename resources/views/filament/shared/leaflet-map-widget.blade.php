@php
    use App\Livewire\Shared\Maps\Marker;
    /** @var array $mapData */
@endphp

<div class="z-0">
    <link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>


    <div id="map" class="rounded-lg" style="width: 100%; height: {{$mapData['mapHeight']}};"></div>
    <script>

        const map = L.map('map').setView([{{$mapData['centerMap']['lat']}}, {{$mapData['centerMap']['lon']}}], {{$mapData['zoomLevel']}});

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

        const obRaceSimple = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/obRaceSimple.png'});
        const obRaceDot = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/obRaceDot.png'});
        const obRaceStages = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/obRaceStages.png'});
        const trainingDot = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/trainingDot.png'});
        const trainingCamp = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/trainingCamp.png'});

        const stageStart = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/trainingDot.png'});
        const defaultMarker = new LeafIcon({iconUrl: '{{ URL::to('/') }}/images/markers/trainingDot.png'});

        @foreach($mapData['markers'] as $marker)
            @php
                /** @var Marker $marker */
                $popupContent = '<strong><span class="text-black">'. $marker->label.'</span></strong>';
                if ($marker->secondaryLabel !== null || $marker->secondaryLabel !== '') {
                    $popupContent .= '<br /> <span class="text-sm text-lime-700 dark:text-lime-700">' . $marker->secondaryLabel .'</span>';
                }

                if (!$mapData['eventMap']) {
                    if ($marker->date !== null) {
                    $popupContent .= '<br /><span class="text-sm text-black">Datum konání: ' . $marker->date->format('d.m.Y') .'</span>';
                    }

                    if ($marker->date !== null && is_array($marker->region)) {
                        foreach($marker->region as $region) {
                            $popupContent .= ' | ' . $region . '</span>';
                        }
                    }

                    if (!$mapData['publicMap']) {
                        if ($marker->eventId !== null) {
                            $popupContent .= '<br /><span class="text-black"><a href="sport-events/' . $marker->eventId . '/entry" target="_blank">Zobrazit akci</a></span>';
                        }
                    } else {
                        $popupContent .= '<br />';
                    }

                    if ($marker->orisId !== null) {
                        if (!$mapData['publicMap']) {
                            $popupContent .= ' | ';
                        }
                        $popupContent .= '<span class="text-black dark:text-black"><a href="https://oris.orientacnisporty.cz/Zavod?id=' . $marker->orisId . '" style="color:black !important;" target="_blank">Zobrazit na ORISu</a></span>';
                    }
                }

            @endphp

            {{--Importatnt to take space--}}
            L.marker([{{$marker->lat}}, {{$marker->lng}}], {icon: {{$marker->markerType->value}}}).addTo(map).bindPopup('{!! $popupContent !!}');

        @endforeach

    </script>
</div>

