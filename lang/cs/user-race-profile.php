<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UserRaceProfile Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings in UserRaceProfile
    | resource.
    |
    */

    'navigation_label' => 'Moje registrace',
    'label'            => 'Moje registrace',
    'plural_label'     => 'Moje registrace',

    // Table
    'table' => [
        'reg_number' => 'Registrace',
        'first_name' => 'Jméno',
        'last_name' => 'Příjmení',
        'street' => 'Ulice',
        'city' => 'Město',
        'zip' => 'PSČ',
        'user-name' => 'Uživatel',
        'oris_id' => 'ID uživatele v ORISu',
        'si' => 'Číslo čipu',
        'email' => 'E-mail',
        'phone' => 'Telefon',
        'active' => 'Aktivní',
        'active_until' => 'Aktivní do',
        'created_at' => 'Založeno',
    ],
    'table_filter' => [
        'registrations' => 'Registrace',
        'all_registrations' => 'Všechny registrace',
        'active_registrations' => 'Aktivní registrace',
        'disable_registrations' => 'Neaktivní registrace',
    ],

    'form' => [
        'section_address'  => 'Adresa nepovinné',
        'section_licence'  => 'Licence',
        'section_user'     => 'Uživatel',
        'street'           => 'Ulice, číslo domu',
        'gender'           => 'Pohlaví',
        'gender_male'      => 'Muž',
        'gender_female'    => 'Žena',
        'oris_id'          => 'Oris ID',
        'club_user_id'     => 'Klub ORIS ID',
        'user_id_helper'   => 'Automaticky přiřazeno uživateli',
        'si'               => 'Si čip',
        'si_helper'        => 'Preferovaný SI čip',
        'licence_ob'       => 'Licence OB',
        'licence_lob'      => 'Licence LOB',
        'licence_mtbo'     => 'Licence MTBO',
        'reg_number_hint'  => '<a href=":help_url" target="_blank">Vyplň registraci a klikni na lupu.</a>',
    ],

    'common' => [
        'si' => 'SI',
    ],

    'actions' => [

        'search_oris' => [
            'validation_title' => 'Formulář vstupy',
            'validation_body'  => 'Vyplň prosím Registrační číslo.',
            'notification_title' => 'ORIS API',
            'error_body'       => 'Nepodařilo se načíst data.',
            'club_error_body'  => 'Nepodařilo se načíst data o klubovém členství uživatele z ORISU.',
            'success_body'     => 'ORIS v pořádku vrátil požadovaná data.',
        ],

        'update_club_oris_id' => [
            'label'                        => 'Aktualizovat ID členů v ORISu',
            'modal_heading'                => 'Aktualizovat ID členů v ORISu',
            'modal_description'            => 'Provede hromadnou aktualizaci ID členů oproti ORISU, potřebné pro přihlášky na závod.',
            'modal_submit'                 => 'Aktualizovat',
            'notification_success_title'   => 'Aktualizace členství závodníků v klubu proběhla v pořádku',
            'notification_success_body'    => 'Členství v klubu proběhlo v pořádku',
            'notification_error_title'     => 'Něco se nepovedlo',
            'notification_error_body'      => 'Něco se nepovedlo, Můžeš vyzkoušet akci zopakovat nebo kontaktuj admina s popisem chyby, děkujeme.',
        ],

    ],

];
