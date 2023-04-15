<?php

use App\Enums\UserCreditStatus;

return [

    /*
    |--------------------------------------------------------------------------
    | UserCredit Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings in UserCredit
    | resource.
    |
    */

    'user' => 'Přidat uživateli',
    'user_profile' => 'Závodní profil',
    'user_source_id' => 'Založil/Změnil',
    'event_name' => 'Závod/událost',
    'status' => 'Status',

    // Credit Resource
    'form' => [
        'type_title' => 'Typ vstupu',
        'price_title' => 'Cena',
        'amount_title' => 'Částka',
        'currency_title' => 'Měna',
        'source_title' => 'Vložil/Upravil',
    ],
    'credit_type_enum' => [
        'in' => 'Vklad',
        'out' => 'Výběr',
        'donation' => 'Dar',
    ],

    'credit_status_enum' => [
        UserCreditStatus::Done->value => 'Hotovo',
        UserCreditStatus::UnAssign->value => 'Nepřiřazeno',
        UserCreditStatus::Open->value => 'Otevřeno',
    ],

    'credit_source_enum' => [
        'user' => 'Uživatel',
        'cron' => 'System',
    ],

    // Table
    'table' => [
        'created_at_title' => 'Transakce',
        'sport_event_title' => 'Závod/Akce',
        'amount_title' => 'Částka',
        'source_user_title' => 'Zapsal',
    ],
];
