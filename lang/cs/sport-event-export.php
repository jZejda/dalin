<?php

use App\Enums\SportEventExportsType;

return [

    /*
    |--------------------------------------------------------------------------
    | SportEventExport Resource
    |--------------------------------------------------------------------------
    |
    | Překlady pro správu exportních výstupů pro pořádání závodů
    | (SportEventExportResource).
    |
    */

    'navigation_label' => 'Výstupy pro pořádání',
    'label'            => 'Výstup pro pořádání',
    'plural_label'     => 'Výstupy pro pořádání',

    'type_enum' => [
        SportEventExportsType::EventEntryListCat->value => 'Startovka kategorie',
        SportEventExportsType::ResultEntryListCat->value => 'Výsledky kategorie',
    ],

    'aside_link_title_enum' => [
        SportEventExportsType::EventEntryListCat->value => 'Startovka',
        SportEventExportsType::ResultEntryListCat->value => 'Výsledky',
    ],

    'form' => [
        'export_type'         => 'Typ exportu',
        'file_type'           => 'Typ souboru',
        'sport_event'         => 'ID závodu',
        'start_time'          => 'Čas 00',
        'sport_event_leg_id'  => 'ID Etapy závodu',
    ],

    'table' => [
        'path'        => 'Cesta',
        'result_path' => 'Cesta k souboru',
    ],

];
