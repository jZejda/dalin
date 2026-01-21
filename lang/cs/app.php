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
    | The following language lines contain the default App stings.
    |
    */

    'common' => [
        'actions' => 'Akce',
    ],

    // Roles
    'tables' => [
        'actions_tooltip' => 'Další akce',
    ],

    // Icons
    'icons' => [
        AppHeroIcons::Trophy->value => 'Trofej',
        AppHeroIcons::Track->value => 'Auto',
        AppHeroIcons::Watch->value => 'Hodiny',
        AppHeroIcons::Tag->value => 'Tag',
        AppHeroIcons::Ticket->value => 'Tiket',
        AppHeroIcons::Trash->value => 'Koš',
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
