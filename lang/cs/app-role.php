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
        AppRoles::SuperAdmin->value => 'Administrátor',
        AppRoles::ClubAdmin->value => 'Admin klubu',
        AppRoles::EventMaster->value => 'Správce závodů',
        AppRoles::Member->value => 'Člen',
        AppRoles::Racer->value => 'Závodník',
        AppRoles::Redactor->value => 'Redaktor',
        AppRoles::EventOrganizer->value => 'Organizátor závodů',
        AppRoles::BillingSpecialist->value => 'Správce financí',
    ],

];
