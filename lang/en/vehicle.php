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

    'name' => 'Name',
    'brand' => 'Brand',
    'type' => 'Vehicle type',
    'description' => 'Description',
    'seats' => 'Number of seats',
    'operator' => 'Operator',
    'consumption' => 'Consumption',
    'consumption_suffix' => 'l/100 km',
    'price_per_km' => 'Price per km',
    'price_per_km_suffix' => 'CZK/km',
    'active' => 'Active',
    'is_default' => 'Default vehicle',
    'is_default_helper' => 'This vehicle is pre-filled automatically when offering transport.',
    'owner' => 'Owner',

    'brand_placeholder' => '—',
    'operator_placeholder' => '—',
    'consumption_placeholder' => '—',
    'price_per_km_placeholder' => '—',

    'type_enum' => [
        VehicleType::PassengerCar->value => 'Passenger car',
        VehicleType::Van->value => 'Van',
        VehicleType::Bus->value => 'Bus',
        VehicleType::Plane->value => 'Plane',
    ],

    'club_vehicle' => 'Club vehicle',
    'club_vehicles' => 'Club vehicles',
    'my_vehicles' => 'My vehicles',
    'add_vehicle' => 'Add vehicle',
    'empty_my_vehicles' => 'You have no vehicle yet',
    'empty_my_vehicles_description' => 'Add a vehicle so you can offer carpooling to races.',
];
