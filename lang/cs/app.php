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
        'actions' => 'Akce',
    ],

    // Shared navigation groups (Filament sidebar)
    'navigation_groups' => [
        'users'         => 'Uživatel',
        'finance'       => 'Správa Financí',
        'content'       => 'Obsah',
        'race_settings' => 'Nastavení závodů',
    ],

    // Roles
    'tables' => [
        'actions_tooltip' => 'Další akce',
    ],

    // Icons
    'icons' => [
        AppHeroIcons::Trophy->value => 'Trofej',
        AppHeroIcons::Track->value => 'Auto',
        AppHeroIcons::Watch->value => 'Stopky',
        AppHeroIcons::Tag->value => 'Tag',
        AppHeroIcons::Ticket->value => 'Tiket',
        AppHeroIcons::Trash->value => 'Koš',
        AppHeroIcons::CalendarDays->value => 'Kalendář dny',
        AppHeroIcons::Clock->value => 'Hodiny',
        AppHeroIcons::Flag->value => 'Vlajka',
        AppHeroIcons::ExclamationCircle->value => 'Vykřičník v kruhu',
    ],

    // Colors
    'colors' => [
        AppColors::Primary->value => 'Primární',
        AppColors::Success->value => 'Úspěch',
        AppColors::Warning->value => 'Varování',
        AppColors::Danger->value => 'Nebezpečí',
        AppColors::Info->value => 'Informace',
        AppColors::Gray->value => 'Šedá',
    ],

];
