<?php

declare(strict_types=1);

namespace App\Enums;

enum SportEventMarkerType: string
{
    case Parking = 'parking';
    case Accommodation = 'accommodationMarker';
    case StageStart = 'stageStart';
    case StageEnd = 'stageEnd';
    case Other = 'other';

    case DefaultMarker = 'defaultMarker';

    // Legacy, pre-map-icon-system variants of "this marker represents the
    // event itself" — kept only so existing/imported rows keep resolving
    // correctly. See isSelectableForNewMarker().
    case ObRaceSimple = 'obRaceSimple';
    case ObRaceDot = 'obRaceDot';
    case ObRaceStages = 'obRaceStages';
    case Training = 'trainingDot';
    case TrainingCamp = 'trainingCamp';

    /**
     * The map icon slug for this marker type, i.e. which partial under
     * resources/views/components/map/icons/ to draw inside the pin.
     *
     * Null means "this marker represents the event itself" — MapMarkerResolver
     * falls back to the event's own sport icon/color/badges for those cases
     * instead of drawing a dedicated auxiliary icon.
     */
    public function iconSlug(): ?string
    {
        return match ($this) {
            self::Parking => 'parking',
            self::StageStart => 'stage-start',
            self::StageEnd => 'stage-end',
            self::Accommodation => 'accommodation',
            self::Other => 'other',

            self::DefaultMarker,
            self::ObRaceSimple,
            self::ObRaceDot,
            self::ObRaceStages,
            self::Training,
            self::TrainingCamp => null,
        };
    }

    /**
     * Whether this type should be offered when manually creating a new marker
     * (SportMarkersRelationManager's "type" select).
     *
     * ObRaceSimple/ObRaceDot/ObRaceStages/Training/TrainingCamp predate the
     * current map icon system: they used to pick the marker's own OB pin
     * style. Today MapMarkerResolver derives the pin's look purely from the
     * event's own sport/stages/discipline, so all five render identically to
     * DefaultMarker — offering them as separate choices only confused users
     * ("every point looks the same on an MTB event"). They remain valid enum
     * cases so existing/imported rows keep resolving, but DefaultMarker is
     * the one supported way to say "draw this point like the event itself".
     */
    public function isSelectableForNewMarker(): bool
    {
        return match ($this) {
            self::ObRaceSimple,
            self::ObRaceDot,
            self::ObRaceStages,
            self::Training,
            self::TrainingCamp => false,

            self::Parking,
            self::Accommodation,
            self::StageStart,
            self::StageEnd,
            self::Other,
            self::DefaultMarker => true,
        };
    }

    public static function enumArray(): array
    {
        $trKey = 'sport-event.type_enum_markers.';

        return [
            self::Parking->value => __($trKey . self::Parking->value),
            self::Accommodation->value => __($trKey . self::Accommodation->value),
            self::StageStart->value => __($trKey . self::StageStart->value),
            self::StageEnd->value => __($trKey . self::StageEnd->value),
            self::Other->value => __($trKey . self::Other->value),
            self::DefaultMarker->value => __($trKey . self::DefaultMarker->value),
            self::ObRaceSimple->value => __($trKey . self::ObRaceSimple->value),
            self::ObRaceDot->value => __($trKey . self::ObRaceDot->value),
            self::ObRaceStages->value => __($trKey . self::ObRaceStages->value),
            self::Training->value => __($trKey . self::Training->value),
            self::TrainingCamp->value => __($trKey . self::TrainingCamp->value),
        ];
    }
}
