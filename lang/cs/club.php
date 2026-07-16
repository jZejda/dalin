<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Club Resource
    |--------------------------------------------------------------------------
    |
    | Překlady pro správu klubů (ClubResource).
    |
    */

    'navigation_label' => 'Kluby',
    'label'            => 'Klub',
    'plural_label'     => 'Kluby',

    'form' => [
        'abbr'   => 'Zkratka',
        'region' => 'Region',
    ],

    'table' => [
        'abbr'        => 'Zkratka',
        'name'        => 'Název',
        'region'      => 'Region',
        'oris_id'     => 'ORIS ID',
        'oris_number' => 'ORIS Number',
    ],

    'actions' => [
        'update' => [
            'label'                     => 'Aktualizovat',
            'modal_heading'             => 'Aktualizuj Kluby z ORISu',
            'modal_description'         => 'Načte a uloží/aktualizuje kluby z ORISu.',
            'modal_submit_action_label' => 'Aktualizovat',
            'notification_title'        => 'Aktualizace klubů',
            'notification_body_success' => 'Nově přidáno klubů: :new | Aktualizováno klubů: :updated',
            'notification_body_error'   => 'Něco se nepovedlo, Můžeš vyzkoušet akci zopakovat nebo kontaktuj admina s popisem chyby, děkujeme.',
        ],
    ],

];
