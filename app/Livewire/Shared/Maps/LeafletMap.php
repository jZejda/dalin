<?php

declare(strict_types=1);

namespace App\Livewire\Shared\Maps;

use App\Enums\SportEventType;
use App\Models\SportEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Component;

final class LeafletMap extends Component
{
    private ?BaseMap $baseMap = null;

    public ?SportEvent $sportEvent = null;

    public ?int $sportType = 1;

    public bool $publicMap = false;

    public function __construct(?BaseMap $baseMap = null)
    {
        $this->baseMap = $baseMap ?? new BaseMap();
    }

    public function render(): View
    {
        if ($this->sportEvent !== null) {
            return view('filament.shared.leaflet-map-widget', [
                'mapData' => [
                    'centerMap' => $this->baseMap?->calculateCenterMapFromEvent($this->sportEvent),
                    'markers' => $this->getMapDataFromEvent(),
                    'eventMap' => true,
                    'zoomLevel' => 13,
                    'mapHeight' => '400px',
                    'publicMap' => $this->publicMap,
                ],
            ]);
        } else {
            return view('filament.shared.leaflet-map-widget', [
                'mapData' => [
                    'centerMap' => $this->baseMap?->calculateCenterFromSportEvents($this->getSportEvents()),
                    'markers' => $this->getMapDataFromEvents(),
                    'eventMap' => false,
                    'zoomLevel' => 8,
                    'mapHeight' => '500px',
                    'publicMap' => $this->publicMap,
                ],
            ]);
        }
    }

    /**
     * @return Marker[]|null
     */
    public function getMapDataFromEvent(): ?array
    {
        return $this->baseMap?->getMarkersFromEvent($this->sportEvent);
    }

    /**
     * @return Marker[]|null
     */
    private function getMapDataFromEvents(): ?array
    {
        return $this->baseMap?->getMarkersFromEvents($this->getSportEvents());
    }

    /**
     * @return Collection|SportEvent[]
     */
    private function getSportEvents(): Collection
    {
        return SportEvent::query()
            ->where('date', '>', Carbon::now()->subDays(2))
            //->where('sport_id', '=', $this->sportType)
            ->where('cancelled', '=', 0)
            ->whereIn('event_type', [
                SportEventType::Race->value,
                SportEventType::Training->value,
                SportEventType::TrainingCamp->value,
            ])
            ->sport($this->sportType)
            ->get();
    }
}
