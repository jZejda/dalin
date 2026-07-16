<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UserEntry Resource
    |--------------------------------------------------------------------------
    |
    | Překlady pro správu přihlášek uživatelů na závody (UserEntryResource).
    |
    */

    'navigation_label' => 'Přihlášky',
    'label'            => 'Přihláška na závody',
    'plural_label'     => 'Přihlášky na závody',

    'table' => [
        'sport_event'          => 'Závod',
        'class_name'           => 'Kategorie',
        'race_profile'         => 'Registrace',
        'date'                 => 'Datum',
        'real_start'           => 'Start v',
        'real_start_none'      => '—',
        'requested_start_note' => 'Poznámka',
        'rent_si'              => 'Půjčít SI',
        'entry_stages'         => 'Etapy',
        'entry_status'         => 'Stav přihlášky',
    ],

    'filters' => [
        'entry_status' => 'Stav přihlášky',
    ],

    'actions' => [
        'view_information' => [
            'label' => 'info',
        ],
    ],

    'infolist' => [
        'entry_section' => [
            'heading'     => 'Přihláška',
            'description' => 'Detail přihlášky na zvolený závod nebo akci.',
        ],
        'sport_event_name'  => 'Název závodu/akce:',
        'sport_event_date'  => 'Datum konání akce:',
        'sport_event_place' => 'Místo:',

        'profile_section' => [
            'heading'     => 'Závodní profil',
            'description' => 'Detail přihlášeného závodníka, plus ostatní přihlašovací údaje.',
        ],
        'class_name'                  => 'Kategorie:',
        'race_profile'                => 'Závodník:',
        'note'                        => 'Poznámka:',
        'note_placeholder'            => '- nebyla vyplněna -',
        'club_note'                   => 'Klubová poznámka:',
        'club_note_placeholder'       => '- nebyla vyplněna -',
        'requested_start'             => 'Start v:',
        'requested_start_placeholder' => '- nebyl požadován -',
        'rent_si'                     => 'Půjčit čip:',
        'entry_stages'                => 'Etapy:',
        'entry_stages_placeholder'    => '- jednoetapový závod -',
    ],

];
