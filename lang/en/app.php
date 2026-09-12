<?php

declare(strict_types=1);

use App\Enums\AppColors;
use App\Enums\AppHeroIcons;

return [

    /*
    |--------------------------------------------------------------------------
    | App
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default App strings.
    |
    */

    'common' => [
        'actions' => 'Actions',
    ],

    // Shared navigation groups (Filament sidebar)
    'navigation_groups' => [
        'users'         => 'User',
        'finance'       => 'Finance',
        'content'       => 'Content',
        'events'        => 'Events/Races',
        'admin'         => 'Administration',
        'race_settings' => 'Race Settings',
    ],

    // Roles
    'tables' => [
        'actions_tooltip' => 'Actions',
    ],

    // Icons
    'icons' => [
        AppHeroIcons::Trophy->value => 'Trophy',
        AppHeroIcons::Track->value => 'Car',
        AppHeroIcons::Watch->value => 'Stopwatch',
        AppHeroIcons::Tag->value => 'Tag',
        AppHeroIcons::Ticket->value => 'Ticket',
        AppHeroIcons::Trash->value => 'Trash',
        AppHeroIcons::CalendarDays->value => 'Calendar days',
        AppHeroIcons::Clock->value => 'Clock',
        AppHeroIcons::Flag->value => 'Flag',
        AppHeroIcons::ExclamationCircle->value => 'Exclamation circle',
    ],

    // Colors
    'colors' => [
        AppColors::Primary->value => 'Primary',
        AppColors::Success->value => 'Success',
        AppColors::Warning->value => 'Warning',
        AppColors::Danger->value => 'Danger',
        AppColors::Info->value => 'Information',
        AppColors::Gray->value => 'Gray',
    ],

];
