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
        AppRoles::Redactor->value => 'Redaktor',
        AppRoles::Member->value => 'Člen',
        AppRoles::BillingSpecialist->value => 'Správce financí',
        AppRoles::EventMaster->value => 'Správce závodů',
        AppRoles::EventOrganizer->value => 'Organizátor závodů',
    ],

];
