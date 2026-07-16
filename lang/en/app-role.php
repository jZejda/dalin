<?php

use App\Enums\AppRoles;

return [

    /*
    |--------------------------------------------------------------------------
    | UserRole Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings for UserRoles
    | resource.
    |
    */

    // Roles
    'app_role_enum' => [
        AppRoles::SuperAdmin->value => 'Administrator',
        AppRoles::ClubAdmin->value => 'Club admin',
        AppRoles::EventMaster->value => 'Event master',
        AppRoles::Member->value => 'Member',
        AppRoles::Racer->value => 'Racer',
        AppRoles::Redactor->value => 'Redactor',
        AppRoles::EventOrganizer->value => 'Event organizer',
        AppRoles::BillingSpecialist->value => 'Billing specialist',
    ],

];
