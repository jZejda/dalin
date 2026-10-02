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

    'seo' => [
        'start_list_title'          => 'Startovka – :event',
        'start_list_title_fallback' => 'Startovka',
        'result_list_title'          => 'Výsledky – :event',
        'result_list_title_fallback' => 'Výsledky',
        'classes' => '{1} :count kategorie|[2,4] :count kategorie|[5,*] :count kategorií',
        'runners' => '{1} :count závodník|[2,4] :count závodníci|[5,*] :count závodníků',
    ],

];
