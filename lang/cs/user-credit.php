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

    'id' => '#ID',
    'user' => 'Přidat uživateli',
    'user_profile' => 'Závodní profil',
    'related_user_profile' => 'Přesun ve prospěch uživatele',
    'user_source_id' => 'Založil/Změnil',
    'event_name' => 'Závod/událost',
    'status' => 'Status',
    'amount' => 'Částka',
    'currency' => 'Měna',
    'note' => 'Poznámka',

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
        'sport_event_date' => 'Datum akce',
        'sport_event_title' => 'Závod/Akce',
        'amount_title' => 'Částka',
        'source_user_title' => 'Zapsal',
        'for_user' => 'Pro uživatele:  ',
        'from_user' => 'Od uživatele:  ',
    ],

    // Actions
    'actions' => [
        'transport_billing' => [
            'action_group_label' => 'Cestovní rozůčtování',
            'modal_description' => 'Vyúčtování cestovních nákladů mezi členy klubu. Jedná se převod financí mezi členy klubu,
            za účelem vyrovnání cestovních nákladů. Akce vytvoří dva záznamy, jeden plusový uživateli kterému se častka připisuje.
            Druhý záznam, kterému se částka strhává.',
            'modal_heading' => 'Vyúčtování cestovních nákladů.',
            'modal_submit_action_label' => 'Přidej vyúčtování',

        ]
    ]
];
