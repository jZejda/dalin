<?php

declare(strict_types=1);

namespace App\View\Components\SportEvent;

use App\Enums\SportEventType;
use App\Models\SportList;
use App\Services\Map\MapMarkerResolver;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class EventIcon extends Component
{
    /** @var array<int, string> */
    private const array SPORT_ICON_SLUGS = [1 => 'ob', 2 => 'lob', 3 => 'mtbo', 4 => 'trail'];

    private const string DEFAULT_COLOR = '#3388FF';

    /** @var array<int, string> */
    private static array $colorCache = [];

    public SportEventType $eventType;

    public int $sportId;

    public string $iconSlug;

    public string $colorHex;

    public ?string $categoryIconSlug;

    public function __construct(SportEventType $eventType, int $sportId, MapMarkerResolver $resolver)
    {
        $this->eventType = $eventType;
        $this->sportId = $sportId;
        $this->iconSlug = self::SPORT_ICON_SLUGS[$sportId] ?? 'ob';

        if (! array_key_exists($sportId, self::$colorCache)) {
            self::$colorCache[$sportId] = optional(SportList::find($sportId))->color ?? self::DEFAULT_COLOR;
        }
        $this->colorHex = self::$colorCache[$sportId];

        $this->categoryIconSlug = $resolver->categoryIconSlugFor($eventType);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sport-event.event-icon');
    }
}
