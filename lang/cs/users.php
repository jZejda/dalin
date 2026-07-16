<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings in User
    | resource.
    |
    */

    'navigation_label' => 'Uživatelé',
    'label'            => 'Uživatel',
    'plural_label'     => 'Uživatelé',

    'form' => [
        'payer_variable_symbol'        => 'Variabilní symbol uživatele',
        'payer_variable_symbol_helper' => 'Mělo by se jednat o první čísla registrace, tedy přesně 4 číslice.',
        'password'                     => 'Heslo',
        'new_password'                 => 'Nové heslo',
        'roles'                        => 'Role',
        'roles_warning'                => '**Info:** Uživateli je potřeba přiřadit minimálně jednu z rolí, jinak nebude mít oprávnění k žádné akci.',
    ],

    // Table
    'table' => [
        'name'            => 'Jméno',
        'email'           => 'E-mail',
        'variable_symbol' => 'VS',
        'roles'           => 'Role',
        'created_at'      => 'Vytvořeno',
        'updated_at'      => 'Upraveno',
    ],

    'table_filter' => [
        'users' => 'Uživatelé',
        'all_users' => 'Všichni uživatelé',
        'active_users' => 'Aktivní uživatelé',
        'disable_users' => 'Neaktivní uživatelé',
    ],

    'actions' => [

        'reset_password' => [
            'label'               => 'Resetovat heslo',
            'modal_heading'       => 'Nové heslo',
            'modal_description'   => 'Resetuje heslo uživateli.<br><br> Po potvrzení se uživatelovi: :user <strong>zašle e-mail s novým heslem.</strong>',
            'field_password'      => 'Nové heslo',
            'notification_title'  => 'Reset hesla',
            'notification_body'   => 'Nové heslo bylo resetováno a odesláno uživateli na jeho e-mailovou schránku: :email.',
        ],

        'change_status' => [
            'label'                  => 'Změnit stav',
            'modal_heading'          => 'Změnit stav uživatele',
            'modal_description'      => 'Aktuální stav uživatele :user je <strong>:status</strong>.<br>Opravdu chcete změnit jeho stav?',
            'field_status'           => 'Stav uživatele',
            'status_active'          => 'Aktivní',
            'status_inactive'        => 'Neaktivní',
            'current_status_active'  => 'aktivní',
            'current_status_inactive' => 'neaktivní',
            'activated'              => 'aktivován',
            'deactivated'            => 'deaktivován',
            'notification_title'     => 'Změna stavu uživatele',
            'notification_body'      => 'Uživatel :user byl :status.',
        ],

    ],

    'user_credit_relation' => [
        'label'          => 'Finance',
        'plural_label'   => 'Finance',
        'title'          => 'Finance',
        'table' => [
            'registration'      => 'Registrace závodníka',
            'amount_total'      => 'Celkem',
            'comments'          => 'Komentářů',
            'record_id'         => 'id: :id',
            'event_internal_id' => 'interní id závodu: :id',
        ],
        'filters' => [
            'sport_event' => 'Závod',
            'created_from' => 'Datum od',
            'created_until' => 'Datum do',
        ],
        'actions' => [
            'export' => [
                'label' => 'Export financi uživatele',
                'col_created_at' => 'Vytvořeno dne',
                'col_event_name' => 'Název události',
                'col_event_alt_name' => 'Alternativní název',
                'col_reg_number' => 'Registrace',
                'col_amount' => 'Částka',
                'col_source_user' => 'Zapsal',
            ],
        ],
    ],

    'race_profile_relation' => [
        'label' => 'Závodní profil',
        'title' => 'Závodní profil',
    ],

];
