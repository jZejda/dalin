<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | MemberFinance Resource
    |--------------------------------------------------------------------------
    |
    | Překlady pro stránku finančního přehledu členů (MemberFinanceResource).
    |
    */

    'navigation_label' => 'Finance členů',
    'label'            => 'Finance člena',
    'plural_label'     => 'Finance členů',

    'infolist' => [
        'section_user'     => 'Informace o uživateli',
        'section_finance'  => 'Stav účtu',
        'name'             => 'Jméno a příjmení',
        'email'            => 'E-mail',
        'balance'          => 'Aktuální zůstatek',
        'variable_symbol'  => 'Variabilní symbol pro platby',
    ],

    'form' => [
        'section_title'          => 'Informace o členovi',
        'name'                   => 'Jméno',
        'email'                  => 'E-mail',
        'payer_variable_symbol'  => 'Variabilní symbol',
        'active'                 => 'Aktivní člen',
        'balance'                => 'Aktuální zůstatek',
    ],

    'table' => [
        'user'             => 'Uživatel',
        'email'            => 'E-mail',
        'registrations'    => 'Registrace',
        'variable_symbol'  => 'Variabilní symbol',
        'active'           => 'Stav',
        'balance'          => 'Zůstatek',
        'status_active'    => 'Aktivní',
        'status_inactive'  => 'Neaktivní',
    ],

    'filters' => [
        'membership_status'  => 'Stav členství',
        'active'             => 'Aktivní',
        'inactive'           => 'Neaktivní',
        'negative_balance'   => 'Záporný zůstatek',
        'zero_balance'       => 'Nulový zůstatek',
        'positive_balance'   => 'Kladný zůstatek',
    ],

    'actions' => [

        'bulk_credit' => [
            'label'              => 'Přidat položku vybraným členům',
            'modal_heading'      => 'Hromadné přidání finanční položky',
            'modal_description'  => 'Vybraná částka bude připsána na konto všech označených členů.',
            'modal_submit'       => 'Přidat položku',
            'field_type'         => 'Typ položky',
            'notification_title' => 'Hromadná operace dokončena',
            'notification_body'  => 'Finanční položka byla přidána :count členům.',
        ],

        'deposit' => [
            'label'              => 'Přidat vklad',
            'modal_heading'      => 'Přidat vklad členovi',
            'modal_description'  => 'Zvolená částka bude připsána na konto člena jako příjem.',
            'modal_submit'       => 'Přidat vklad',
            'field_type'         => 'Typ vkladu',
            'amount_tooltip'     => 'Zadej kladnou částku. Bude připsána na konto.',
            'notification_title' => 'Vklad přidán',
            'notification_body'  => 'Vklad byl úspěšně připsán na konto člena.',
            'type_donation'      => 'Mimořádný členský vklad',
            'type_initial'       => 'Počáteční vklad',
            'type_membership'    => 'Členský příspěvek',
        ],

        'export' => [
            'label'           => 'Export do souboru',
            'col_name'        => 'Jméno',
            'col_email'       => 'E-mail',
            'col_variable_symbol' => 'Variabilní symbol',
            'col_balance'     => 'Zůstatek (CZK)',
            'col_active'      => 'Aktivní',
            'active_yes'      => 'Ano',
            'active_no'       => 'Ne',
        ],

        'deduct' => [
            'label'              => 'Odečíst platbu',
            'modal_heading'      => 'Odečíst platbu členovi',
            'modal_description'  => 'Zvolená částka bude odečtena z konta člena.',
            'modal_submit'       => 'Odečíst platbu',
            'field_type'         => 'Typ platby',
            'amount_tooltip'     => 'Zadej kladnou částku. Bude odečtena z konta.',
            'notification_title' => 'Platba odečtena',
            'notification_body'  => 'Platba byla úspěšně odečtena z konta člena.',
            'type_cashout'       => 'Výdej',
            'type_membership'    => 'Členský příspěvek',
            'type_transport'     => 'Cestovní náklady',
        ],

    ],

    'common' => [
        'amount'               => 'Částka (CZK)',
        'amount_tooltip_bulk'  => 'Kladná hodnota = příjem, záporná = výdaj.',
        'note'                 => 'Poznámka',
        'note_optional'        => 'Volitelné',
    ],

];
