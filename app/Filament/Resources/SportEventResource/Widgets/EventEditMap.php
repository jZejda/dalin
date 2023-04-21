<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Widgets;

use App\Enums\SportEventMarkerType;
use DB;
use App\Enums\SportEventType;
use App\Shared\Helpers\EmptyType;
use App\Models\SportEvent;
use App\Models\SportEventMarker;
use App\Models\UserEntry;
use Illuminate\Support\Str;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Illuminate\Support\Collection;
use Webbingbrasil\FilamentMaps\Actions;
use Webbingbrasil\FilamentMaps\Actions\CenterMapAction;
use Webbingbrasil\FilamentMaps\Marker;
use Webbingbrasil\FilamentMaps\Widgets\MapWidget;

class EventEditMap extends MapWidget
{
    protected int | string | array $columnSpan = 2;

    protected bool $hasBorder = false;

    protected string $height = '250px';

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

    private function getAppropriateEvents(): Collection
    {
        return DB::table('sport_events')
            ->where('cancelled', '!=', 1)
            ->where('gps_lat', '!=', 0.0)
            ->where('gps_lon', '!=', 0.0)
            ->get();
    }
}
