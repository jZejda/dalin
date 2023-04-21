<?php

use App\Enums\SportEventType;
use App\Enums\SportEventMarkerType;

return [

    /*
    |--------------------------------------------------------------------------
    | SportEvent Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings in SportEvent
    | resource.
    |
    */

    'event_type' => 'Typ akcí',

    'type_enum' => [
        SportEventType::Race->value => 'Závod',
        SportEventType::Training->value => 'Trénink',
        SportEventType::TrainingCamp->value => 'Soustředění',
        SportEventType::Other->value => 'Ostatní',
    ],

    'type_enum_markers' => [
        SportEventMarkerType::StageStart->value => 'Start etapy',
        SportEventMarkerType::Parking->value => 'Parkování',
        SportEventMarkerType::Other->value => 'Ostatní',
    ]
];
