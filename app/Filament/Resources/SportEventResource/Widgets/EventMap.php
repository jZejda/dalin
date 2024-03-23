<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Widgets;

use App\Enums\SportEventType;
use App\Models\SportEvent;
use App\Models\SportEventMarker;
use App\Shared\Helpers\EmptyType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

use Webbingbrasil\FilamentMaps\Actions;
use Webbingbrasil\FilamentMaps\Marker;
use Webbingbrasil\FilamentMaps\Widgets\MapWidget;

class EventMap extends MapWidget
{
    public SportEvent $model;

    protected int | string | array $columnSpan = 2;

    protected bool $hasBorder = false;

    protected string $height = '300px';

    protected string | array  $tileLayerUrl = [
        'OpenStreetMap' => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        'OpenTopoMap' => 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png'
    ];

    protected array $tileLayerOptions = [
        'OpenStreetMap' => [
            'attribution' => 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
        ],
        'OpenTopoMap' => [
            'attribution' => 'Map data © <a href="https://openstreetmap.org">OpenStreetMap</a> contributors, SRTM | Map style © <a href="https://opentopomap.org">OpenTopoMap</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-SA</a>)',
        ],
    ];

    public function setUp(): void
    {

        /** @var SportEvent[] $sportEvents */
        $sportEvents = $this->getAppropriateEvents();

        $boundCoords = [];
        foreach($sportEvents as $sportEvent) {
            $boundCoords[] = [
                $sportEvent->gps_lat,
                $sportEvent->gps_lon,
            ];
        }

        //$this->fitBounds($boundCoords); // todele ti omezi mapu na body se zoomem
        $coords = $this->getCenterMapEvent();
        $this->mapOptions(['center' => [$coords['lat'], $coords['lng']], 'zoom' => 12]);
    }

    public function getActions(): array
    {
        $coords = $this->getCenterMapEvent();
        return [
            Actions\CenterMapAction::make()->centerTo([$coords['lat'], $coords['lng']])->zoom(12),
        ];
    }

    public function getMarkers(): array
    {
        /** @var SportEvent[] $markers */
        $markers = $this->getAppropriateEvents();

        $eventMarkers = [];
        foreach ($markers as $marker) {
            $eventMarkers[] = Marker::make((string)$marker->id)
                ->lat((float)$marker->gps_lat)
                ->lng((float)$marker->gps_lon)
                ->color($this->getMarkerByEventType($marker->event_type))
//                ->icon(
//                    'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
//                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
//                    iconSize: [25, 41],
//                    iconAnchor: [12, 41],
//                    popupAnchor: [1, -34],
//                    shadowSize: [41, 41]
//                )
                ->popup(EmptyType::stringNotEmpty($marker->alt_name) ? $marker->name .' | ' . $marker->alt_name : $marker->name)
                ->tooltip(EmptyType::stringNotEmpty($marker->alt_name) ? $marker->name .' | ' . $marker->alt_name : $marker->name);
        }

        /** @var SportEventMarker $eventMarker*/
        foreach($this->getAdditionalMarkers() as $eventMarker) {
            $eventMarkers[] = Marker::make((string)$eventMarker->id)
                ->lat((float)$eventMarker->lat)
                ->lng((float)$eventMarker->lon)
                ->color('yellow')
                ->tooltip(EmptyType::stringNotEmpty($eventMarker->desc) ? $eventMarker->label .' | ' . $eventMarker->desc : $eventMarker->label);

        }
        return $eventMarkers;
    }

    private function getMarkerByEventType(string $eventType): string
    {
        return match ($eventType) {
            SportEventType::Race->value => Marker::COLOR_GREEN,
            SportEventType::Training->value => Marker::COLOR_YELLOW,
            SportEventType::TrainingCamp->value => Marker::COLOR_VIOLET,
            SportEventType::Other->value => Marker::COLOR_ORANGE,
            default => Marker::COLOR_BLUE,
        };
    }

    private function getCenterMapEvent(): array
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('/', $uri);

        /** @var SportEvent $event */
        $event = DB::table('sport_events')
            ->where('cancelled', '!=', 1)
            //->where('id', '=', (int)$uri[3])
            ->where('id', '=', $this->model->id)
            ->where('gps_lat', '!=', 0.0)
            ->where('gps_lon', '!=', 0.0)
            ->first();

        if (is_null($event)) {
            return [
                'lat' => 49.2062264,
                'lng' => 16.6066842,
            ];
        }

        return [
            'lat' => (float)$event->gps_lat,
            'lng' => (float)$event->gps_lon
        ];

    }

    private function getAppropriateEvents(): Collection
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('/', $uri);


        return DB::table('sport_events')
            ->where('cancelled', '!=', 1)
//            ->where('id', '=', (int)$uri[3])
            ->where('id', '=', $this->model->id)
            ->where('gps_lat', '!=', 0.0)
            ->where('gps_lon', '!=', 0.0)
            ->get();
    }

    private function getAdditionalMarkers(): Collection
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = explode('/', $uri);

        return DB::table('sport_event_markers')
            ->where('sport_event_id', '=', $this->model->id)
            ->get();
    }
}
