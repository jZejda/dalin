<?php

declare(strict_types=1);

namespace App\Enums;

enum SportEventMarkerType: string
{
    case DefaultMarker = 'defaultMarker';

    case ObRaceSimple = 'obRaceSimple';
    case ObRaceDot = 'obRaceDot';
    case ObRaceStages = 'obRaceStages';

    case StageStart = 'stageStart';
    case Parking = 'parking';
    case Other = 'other';

    case Training = 'trainingDot';
    case TrainingCamp = 'trainingCamp';

    public static function enumArray(): array
    {
        return [
            'defaultMarker' => __('sport-event.type_enum_markers.'.self::StageStart->value),
            'raceSimple' => __('sport-event.type_enum_markers.'.self::StageStart->value),
            'stageStart' => __('sport-event.type_enum_markers.'.self::StageStart->value),
            'parking' => __('sport-event.type_enum_markers.'.self::Parking->value),
            'other' => __('sport-event.type_enum_markers.'.self::Other->value),

            'training' => __('sport-event.type_enum_markers.'.self::Training->value),
            'trainingCamp' => __('sport-event.type_enum_markers.'.self::TrainingCamp->value),
        ];
    }
}
