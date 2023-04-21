<?php

declare(strict_types=1);

namespace App\Enums;

enum SportEventMarkerType: string
{
    case StageStart = 'stageStart';
    case Parking = 'parking';
    case Other = 'other';

    public static function enumArray(): array
    {
        return [
            'stageStart' => __('sport-event.type_enum_markers.' . self::StageStart->value),
            'parking' => __('sport-event.type_enum_markers.' . self::Parking->value),
            'other' => __('sport-event.type_enum_markers.' . self::Other->value),
        ];
    }
}
