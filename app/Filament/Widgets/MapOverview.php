<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\SportEventType;
use DB;
use App\Models\SportEvent;
use App\Shared\Helpers\EmptyType;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Support\Collection;
use Webbingbrasil\FilamentMaps\Actions;
use Webbingbrasil\FilamentMaps\Actions\CenterMapAction;
use Webbingbrasil\FilamentMaps\Marker;
use Webbingbrasil\FilamentMaps\Widgets\MapWidget;

class MapOverview extends MapWidget
{
    use HasWidgetShield;
    //    use HasDarkModeTiles;

    protected int | string | array $columnSpan = 2;

    protected bool $hasBorder = false;

    protected string $height = '500px';

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

        $this->fitBounds($boundCoords);
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
        return $eventMarkers;
    }

    public function getActions(): array
    {
        return [
            Actions\ZoomAction::make(),
            Actions\CenterMapAction::make()->zoom(2),
            Actions\Action::make('mode')
                ->icon('filamentmapsicon-o-square-3-stack-3d')
                ->callback('setTileLayer(mode === "OpenStreetMap" ? "OpenTopoMap" : "OpenStreetMap")'),
            CenterMapAction::make()->fitBounds($this->getFitBounds()),


        ];
    }

    private function getAppropriateEvents(): Collection
    {
        return DB::table('sport_events')
            ->where('cancelled', '!=', 1)
            ->where('gps_lat', '!=', 0.0)
            ->where('gps_lon', '!=', 0.0)
            ->get();
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
}
