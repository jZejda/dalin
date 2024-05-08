<?php

declare(strict_types=1);

namespace App\Enums;

enum SportEventType: string
{
    case Race = 'race';
    case Training = 'training';
    case TrainingCamp = 'trainingCamp';
    case Other = 'other';

    public static function enumArray(): array
    {
        return [
            'race' => __('sport-event.type_enum.'.self::Race->value),
            'training' => __('sport-event.type_enum.'.self::Training->value),
            'trainingCamp' => __('sport-event.type_enum.'.self::TrainingCamp->value),
            'other' => __('sport-event.type_enum.'.self::Other->value),
        ];
    }
}
