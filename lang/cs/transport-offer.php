<?php

use App\Enums\TransportOfferDirection;

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
    'credit_type' => 'Cesta typ',
    'distance' => 'Vzdálenost [km]',
    'driver' => 'Řidič',
    'description' => 'Doplňkové informace',
    'transport_contribution' => 'Spoluúčast [Kč/km]',

    'type_enum_directions' => [
        TransportOfferDirection::BothDirection->value => 'Oba směry',
        TransportOfferDirection::OnlyThere->value => 'Cesta na závod',
        TransportOfferDirection::OnlyBack->value => 'Cesta zpět ze závodu',
    ],
];
