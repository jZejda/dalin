<?php

declare(strict_types=1);

namespace App\Services\Map;

use App\Enums\SportEventMarkerType;
use App\Enums\SportEventType;
use App\Models\SportEvent;
use App\Models\SportEventMarker;

/**
 * Single source of truth for how a SportEvent (or one of its auxiliary
 * SportEventMarker points) should look on the map: sport icon + color,
 * the E(tapy)/R(elay) corner badge, and the category badge for
 * Training/TrainingCamp/ClubChampionship/Other.
 *
 * See docs/map-icons.md for the icon taxonomy and how to add a new one.
 */
final class MapMarkerResolver
{
    private const string DEFAULT_SPORT_COLOR = '#3388FF';

    private const string AUX_COLOR = '#616161';

    private const string DEFAULT_SPORT_ICON_SLUG = 'ob';

    /**
     * Every sport icon slug that actually has a partial under
     * resources/views/components/map/icons/. sport_lists.short_name is free-text
     * (no Filament resource exists for it today, but it's still untrusted DB
     * content) and is never interpolated into a view name without this check —
     * it feeds a Blade @include, so an unrecognized value must not pass through.
     *
     * @var list<string>
     */
    private const array ALLOWED_SPORT_ICON_SLUGS = ['ob', 'lob', 'mtbo', 'trail'];

    public function resolveForEvent(SportEvent $sportEvent): MapMarkerVisual
    {
        return new MapMarkerVisual(
            iconSlug: $this->iconSlugForSport($sportEvent->sport->short_name ?? null),
            colorHex: $sportEvent->sport->color ?? self::DEFAULT_SPORT_COLOR,
            modifierLetter: $this->resolveModifierLetter($sportEvent),
            categoryIconSlug: $this->categoryIconSlugFor($sportEvent->event_type),
        );
    }

    /**
     * Validates a sport_lists.short_name value against the actual icon partials that
     * exist, falling back to the default sport icon for anything unrecognized. Public
     * so the icon gallery page (Filament) can preview every SportList row the same way
     * the map does, without duplicating the whitelist.
     */
    public function iconSlugForSport(?string $shortName): string
    {
        $slug = strtolower($shortName ?? self::DEFAULT_SPORT_ICON_SLUG);

        return in_array($slug, self::ALLOWED_SPORT_ICON_SLUGS, true) ? $slug : self::DEFAULT_SPORT_ICON_SLUG;
    }

    public function resolveForMarker(SportEventMarker $marker, SportEvent $sportEvent): MapMarkerVisual
    {
        return match ($marker->type) {
            null,
            SportEventMarkerType::DefaultMarker,
            SportEventMarkerType::ObRaceSimple,
            SportEventMarkerType::ObRaceDot,
            SportEventMarkerType::ObRaceStages,
            SportEventMarkerType::Training,
            SportEventMarkerType::TrainingCamp => $this->resolveForEvent($sportEvent),

            SportEventMarkerType::Parking => new MapMarkerVisual('parking', self::AUX_COLOR, $marker->letter, null),
            SportEventMarkerType::StageStart => new MapMarkerVisual('stage-start', self::AUX_COLOR, $marker->letter, null),
            SportEventMarkerType::StageEnd => new MapMarkerVisual('stage-end', self::AUX_COLOR, $marker->letter, null),
            SportEventMarkerType::Accommodation => new MapMarkerVisual('accommodation', self::AUX_COLOR, $marker->letter, null),
            SportEventMarkerType::Other => new MapMarkerVisual('other', self::AUX_COLOR, $marker->letter, null),
        };
    }

    /**
     * The small corner badge denoting an event's category. Race (and a missing/legacy
     * event type) gets none — the default/clean look — every other type gets a distinct badge.
     */
    public function categoryIconSlugFor(?SportEventType $eventType): ?string
    {
        return match ($eventType) {
            null, SportEventType::Race => null,
            SportEventType::Training => 'training',
            SportEventType::TrainingCamp => 'training-camp',
            SportEventType::ClubChampionship => 'club-championship',
            SportEventType::Other => 'other',
        };
    }

    /**
     * The neutral background color used for all auxiliary point icons (parking,
     * stage start/end, accommodation, other). Exposed so the icon gallery page can
     * preview them with the exact same color instead of hardcoding it again.
     */
    public function auxColor(): string
    {
        return self::AUX_COLOR;
    }

    private function resolveModifierLetter(SportEvent $sportEvent): ?string
    {
        if ($sportEvent->isRelayDiscipline()) {
            return 'R';
        }

        if ($sportEvent->stages !== null && $sportEvent->stages > 1) {
            return 'E';
        }

        return null;
    }
}
