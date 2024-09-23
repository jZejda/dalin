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
        $trKey = 'sport-event.type_enum_markers.';

        return [
            self::DefaultMarker->value => __($trKey . self::DefaultMarker->value),
            self::ObRaceSimple->value => __($trKey . self::ObRaceSimple->value),
            self::ObRaceDot->value => __($trKey . self::ObRaceDot->value),
            self::ObRaceStages->value => __($trKey . self::ObRaceStages->value),
            self::StageStart->value => __($trKey . self::StageStart->value),
            self::Parking->value => __($trKey . self::Parking->value),
            self::Other->value => __($trKey . self::Other->value),
            self::Training->value => __($trKey . self::Training->value),
            self::TrainingCamp->value => __($trKey . self::TrainingCamp->value),
        ];
    }
}
