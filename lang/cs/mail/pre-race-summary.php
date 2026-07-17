<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | PreRaceSummaryMail e-mail
    |--------------------------------------------------------------------------
    |
    | Předmět a tělo e-mailu se souhrnem informací před závodem.
    |
    */

    'subject' => [
        'pre_race_summary' => 'Souhrn před závodem: :event',
    ],

    'body' => [
        'heading'            => 'Souhrn před závodem: :event',
        'info_heading'       => 'Informace o akci',
        'date_label'         => 'Datum',
        'days_count'         => '{1} :count den|[2,4] :count dny|[5,*] :count dní',
        'start_time_label'   => 'Start první etapy',
        'oris_label'         => 'ORIS',
        'oris_link_text'     => 'Závod :id',
        'profiles_heading'   => 'Závodní profily',
        'reg_number_label'   => 'Reg. číslo',
        'name_label'         => 'Jméno',
        'category_label'     => 'Kategorie',
        'plus_min_label'     => '+min',
        'distance_label'     => 'Délka',
        'controls_label'     => 'Kontroly',
        'climbing_label'     => 'Převýšení',
        'description_heading' => 'Popis akce',
        'links_heading'      => 'Linky',
        'link_name_label'    => 'Název',
        'link_url_label'     => 'Odkaz',
        'news_heading'       => 'Novinky',
    ],

];
