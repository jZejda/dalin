<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UserEntry Resource
    |--------------------------------------------------------------------------
    |
    | Translations for user race entry management (UserEntryResource).
    |
    */

    'navigation_label' => 'Entries',
    'label'            => 'Race entry',
    'plural_label'     => 'Race entries',

    'table' => [
        'sport_event'          => 'Race',
        'class_name'           => 'Class',
        'race_profile'         => 'Registration',
        'date'                 => 'Date',
        'real_start'           => 'Start at',
        'real_start_none'      => '—',
        'requested_start_note' => 'Note',
        'rent_si'              => 'Rent SI',
        'entry_stages'         => 'Stages',
        'entry_status'         => 'Entry status',
    ],

    'filters' => [
        'entry_status' => 'Entry status',
    ],

    'actions' => [
        'view_information' => [
            'label' => 'info',
        ],
    ],

    'infolist' => [
        'entry_section' => [
            'heading'     => 'Entry',
            'description' => 'Detail of the entry for the selected race or event.',
        ],
        'sport_event_name'  => 'Race/event name:',
        'sport_event_date'  => 'Event date:',
        'sport_event_place' => 'Place:',

        'profile_section' => [
            'heading'     => 'Race profile',
            'description' => 'Detail of the entered racer, plus other entry data.',
        ],
        'class_name'                  => 'Class:',
        'race_profile'                => 'Racer:',
        'note'                        => 'Note:',
        'note_placeholder'            => '- not filled in -',
        'club_note'                   => 'Club note:',
        'club_note_placeholder'       => '- not filled in -',
        'requested_start'             => 'Start at:',
        'requested_start_placeholder' => '- not requested -',
        'rent_si'                     => 'Rent chip:',
        'entry_stages'                => 'Stages:',
        'entry_stages_placeholder'    => '- single-stage race -',
    ],

];
