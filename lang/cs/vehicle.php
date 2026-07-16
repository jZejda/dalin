<?php

use App\Enums\VehicleType;

return [

    /*
    |--------------------------------------------------------------------------
    | Vehicle Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings for vehicles
    | in the transport module.
    |
    */

    'name' => 'Název',
    'brand' => 'Značka',
    'type' => 'Typ vozidla',
    'description' => 'Popis',
    'seats' => 'Počet míst',
    'operator' => 'Provozovatel',
    'consumption' => 'Spotřeba',
    'consumption_suffix' => 'l/100 km',
    'price_per_km' => 'Cena za km',
    'price_per_km_suffix' => 'Kč/km',
    'active' => 'Aktivní',
    'is_default' => 'Výchozí vozidlo',
    'is_default_helper' => 'Toto vozidlo se při nabízení dopravy předvyplní automaticky.',
    'owner' => 'Vlastník',

    'brand_placeholder' => '—',
    'operator_placeholder' => '—',
    'consumption_placeholder' => '—',
    'price_per_km_placeholder' => '—',

    'type_enum' => [
        VehicleType::PassengerCar->value => 'Osobní automobil',
        VehicleType::Van->value => 'Dodávka',
        VehicleType::Bus->value => 'Autobus',
        VehicleType::Plane->value => 'Letadlo',
    ],

    'club_vehicle' => 'Klubové vozidlo',
    'club_vehicles' => 'Klubová vozidla',
    'my_vehicles' => 'Moje vozidla',
    'add_vehicle' => 'Přidat vozidlo',
    'empty_my_vehicles' => 'Zatím nemáš žádné vozidlo',
    'empty_my_vehicles_description' => 'Přidej si vozidlo, ať můžeš nabízet spolujízdu na závody.',
];
