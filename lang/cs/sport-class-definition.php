<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SportClassDefinition Resource
    |--------------------------------------------------------------------------
    |
    | Překlady pro správu definic kategorií (SportClassDefinitionResource).
    |
    */

    'navigation_label' => 'Definice kategorií',
    'label'            => 'Definice kategorie',
    'plural_label'     => 'Definice kategorií',

    'form' => [
        'name'         => 'Název',
        'gender'       => 'Pohlaví',
        'gender_female' => 'Žena',
        'gender_male'   => 'Muž',
        'gender_all'    => 'Vše',
        'age_from'     => 'Věk od:',
        'age_to'       => 'Věk do:',
        'sport'        => 'Sport',
        'oris_id'      => 'ORIS ID',
    ],

    'table' => [
        'age_from' => 'Věk od',
        'age_to'   => 'Věk do',
        'sport'    => 'Sport',
        'oris_id'  => 'ORIS ID',
    ],

    'actions' => [
        'update' => [
            'label'                     => 'Aktualizovat',
            'modal_heading'             => 'Aktualizuj definici kategorií z ORISu',
            'modal_description'         => 'Definice kategorii, podle ORISU. V dialogu zvol pro jaký sport chceš zaktualizovat definice kategorií.',
            'modal_submit_action_label' => 'Aktualizovat',
            'sport_event'               => 'Závod/událost',
            'notification_title'        => 'Aktualizace definic kategorií',
            'notification_body_success' => 'Aktualizace definic kategorií z ORISu proběhla v pořádku.',
            'notification_body_error'   => 'Něco se nepovedlo, Můžeš vyzkoušet akci zopakovat nebo kontaktuj admina s popisem chyby, děkujeme.',
        ],
    ],

];
