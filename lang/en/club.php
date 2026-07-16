<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Club Resource
    |--------------------------------------------------------------------------
    |
    | Translations for managing clubs (ClubResource).
    |
    */

    'navigation_label' => 'Clubs',
    'label'            => 'Club',
    'plural_label'     => 'Clubs',

    'form' => [
        'abbr'   => 'Abbreviation',
        'region' => 'Region',
    ],

    'table' => [
        'abbr'        => 'Abbreviation',
        'name'        => 'Name',
        'region'      => 'Region',
        'oris_id'     => 'ORIS ID',
        'oris_number' => 'ORIS Number',
    ],

    'actions' => [
        'update' => [
            'label'                     => 'Update',
            'modal_heading'             => 'Update clubs from ORIS',
            'modal_description'         => 'Loads and saves/updates clubs from ORIS.',
            'modal_submit_action_label' => 'Update',
            'notification_title'        => 'Clubs update',
            'notification_body_success' => 'Newly added clubs: :new | Updated clubs: :updated',
            'notification_body_error'   => 'Something went wrong. You can try the action again or contact the admin with a description of the error, thank you.',
        ],
    ],

];
