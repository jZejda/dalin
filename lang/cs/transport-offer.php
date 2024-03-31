<?php

use App\Enums\TransportOfferDirection;
use App\Enums\TransportType;

return [

    /*
    |--------------------------------------------------------------------------
    | TransportOffer Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings in TransportOffer
    | resource.
    |
    */

    'free_seats' => 'Volných míst',
    'direction' => 'Typ cesty',
    'distance' => 'Vzdálenost [km]',
    'driver' => 'Řidič',
    'description' => 'Doplňkové informace',
    'transport_contribution' => 'Spoluúčast [Kč/km]',

    'type_enum_directions' => [
        TransportOfferDirection::BothDirection->value => 'Oba směry',
        TransportOfferDirection::OnlyThere->value => 'Cesta na závod',
        TransportOfferDirection::OnlyBack->value => 'Cesta zpět ze závodu',
    ],
    'type_enum_type' => [
        TransportType::Car->value => 'Auto',
        TransportType::Microbus->value => 'Dodávka',
        TransportType::Bus->value => 'Autobus',
    ]
];
