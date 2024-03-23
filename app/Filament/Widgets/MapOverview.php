<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\SportEventType;
use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;
use App\Models\SportEvent;
use App\Shared\Helpers\EmptyType;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;
use stdClass;

use Webbingbrasil\FilamentMaps\Actions;
use Webbingbrasil\FilamentMaps\Actions\CenterMapAction;
use Webbingbrasil\FilamentMaps\Marker;
use Webbingbrasil\FilamentMaps\Widgets\MapWidget;

class MapOverview extends MapWidget
{
    // use HasWidgetShield;
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
        $markers = $this->getAppropriateEvents();
        $eventMarkers = [];
        foreach ($markers as $marker) {
            $eventMarkers[] = Marker::make((string)$marker->id)
                ->lat((float)$marker->gps_lat)
                ->lng((float)$marker->gps_lon)
                ->icon(
                    URL::to('/') . '/images/markers/' . $this->getMarkerByEventType($marker) . '.png',
                    URL::to('/') . '/images/markers/marker-shadow.png',
                    [32, 36],
                    [16, 34],
                    [-3, -38],
                    [41, 41],
                )
                ->popup($this->getMarkerHtmlTooltipData($marker));
        }
        return $eventMarkers;
    }

    private function getMarkerHtmlTooltipData(SportEvent|stdClass $sportEvent): string
    {
        if (EmptyType::stringNotEmpty($sportEvent->alt_name)) {
            $eventAltName = ' | ' . $sportEvent->alt_name;
        } else {
            $eventAltName = '';
        }

        return '<a href="admin/sport-events/' . $sportEvent->id . '/entry" target="_blank">' . $sportEvent->name . '</a>' . $eventAltName
            . '<br>'
            . 'datum: <b>' . $sportEvent->date->format(AppHelper::DATE_FORMAT) . '</b>'
            . '<br>'
            . 'místo: <b>' . $sportEvent->place . '</b>'
            . '<br>'
            . 'ORIS: <b><a href="https://oris.orientacnisporty.cz/Zavod?id=' . $sportEvent->oris_id . '" target="_blank">' . $sportEvent->oris_id . '</a></b>';
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

    /**
     * @return Collection<SportEvent>
     */
    private function getAppropriateEvents(): Collection
    {
        return SportEvent::query()
            ->where('cancelled', '!=', 1)
            ->where('gps_lat', '!=', 0.0)
            ->where('gps_lon', '!=', 0.0)
            ->where('date', '>', Carbon::now()->subMonths(2))
            ->whereIn('event_type', [SportEventType::Race, SportEventType::Training, SportEventType::TrainingCamp])
            ->get();
    }

    private function getMarkerByEventType(SportEvent $sportEvent): string
    {
        if ($sportEvent->sport_id === 1) {
            if ($sportEvent->event_type === SportEventType::Race) {
                if ($sportEvent->stages > 2) {
                    return 'ob-race-stages';
                } else {
                    return 'ob-race-simple';
                }
            } elseif ($sportEvent->event_type === SportEventType::Training) {
                return 'training-dot';
            } elseif ($sportEvent->event_type === SportEventType::TrainingCamp) {
                return 'training-camp';
            }
        }

        return 'ob-race-simple';
    }
}
