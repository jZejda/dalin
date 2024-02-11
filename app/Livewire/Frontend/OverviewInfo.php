<?php

declare(strict_types=1);

namespace App\Livewire\Frontend;

use App\Livewire\Shared\Maps\BaseMap;
use App\Livewire\Shared\Maps\MapBuilder;
use App\Livewire\Shared\Maps\Marker;
use App\Models\SportEvent;
use App\Models\SportEventMarker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class OverviewInfo extends Component
{
    private ?BaseMap $baseMap = null;

    public function __construct(?MapBuilder $baseMap = null)
    {
        $this->baseMap = $baseMap ?? new BaseMap();
    }

    public function render()
    {
        $markers = $this->getMapDataFromEvents();
        return view('livewire.frontend.overview-info', [
            'mapData' => [
                'centerMap' => $this->baseMap->getCenterMapCoordsFromMarkers($markers),
                'markers' => $markers,
            ],
        ]);
    }

    /**
     * @return Marker[]
     */
    private function getMapDataFromEvents(): array
    {
        /** @var SportEventMarker[] $markers */
        return $this->baseMap->getMarkersFromEvents($this->getSportEvents());
    }

    private function getSportEvents(): array|Collection
    {
        return SportEvent::query()
            ->where('date', '>', Carbon::now())
            ->get();
    }
}
