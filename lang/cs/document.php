<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Dokumenty',
    'label'            => 'Dokument',
    'plural_label'     => 'Dokumenty',

    'form' => [
        'title'               => 'Název',
        'slug'                => 'Adresa',
        'slug_helper'         => 'Stálá adresa dokumentu. Když později nahrajete nový soubor, odkaz zůstane stejný. Po zveřejnění adresu raději neměňte, staré odkazy by přestaly fungovat.',
        'description'         => 'Popis',
        'description_helper'  => 'Nepovinné. Krátká věta pod názvem na veřejné stránce.',
        'file'                => 'Soubor',
        'file_helper'         => 'PDF, Word, Excel, OpenDocument nebo obrázek, max. :max MB. Nový soubor nahradí ten stávající.',
        'category'            => 'Kategorie',
        'year'                => 'Rok',
        'year_helper'         => 'Rok, ke kterému se dokument vztahuje (např. výroční zpráva za 2025). U stanov lze nechat prázdné.',
        'is_public'           => 'Veřejný dokument',
        'is_public_helper'    => 'Vypnuto: dokument uvidí a stáhnou jen přihlášení členové a nedostane se do vyhledávačů.',
        'sort'                => 'Pořadí',
        'sort_helper'         => 'Menší číslo je výš. Dokumenty se řadí podle roku (nejnovější první) a pak podle pořadí.',
    ],

    'table' => [
        'title'             => 'Název',
        'category'          => 'Kategorie',
        'year'              => 'Rok',
        'is_public'         => 'Veřejný',
        'size'              => 'Velikost',
        'uploaded_by'       => 'Nahrál',
        'updated_at'        => 'Upraveno',
        'empty_heading'     => 'Zatím žádné dokumenty',
        'empty_description' => 'Nahrajte výroční zprávu, stanovy nebo zápis ze schůze. Veřejné dokumenty se zobrazí na stránce Dokumenty.',
    ],

    'filters' => [
        'category'  => 'Kategorie',
        'is_public' => 'Veřejný',
    ],

    'actions' => [
        'open' => [
            'label' => 'Otevřít soubor',
        ],
        'public_list' => [
            'label' => 'Veřejná stránka',
        ],
    ],

    'enums' => [
        'category' => [
            'annual_report' => 'Výroční zprávy',
            'statutes'      => 'Stanovy a předpisy',
            'minutes'       => 'Zápisy ze schůzí',
            'other'         => 'Ostatní',
        ],
    ],

    'public' => [
        'title'         => 'Dokumenty',
        'description'   => 'Výroční zprávy, stanovy a zápisy ze schůzí klubu :club ke stažení.',
        'lead'          => 'Výroční zprávy, stanovy a další dokumenty klubu ke stažení.',
        'empty'         => 'Zatím tu nejsou žádné dokumenty.',
        'members_only'  => 'Jen pro členy',
        'members_hint'  => 'Interní dokumenty, třeba zápisy ze schůzí, uvidí členové po přihlášení.',
        'members_login' => 'Přihlásit se',
    ],
];
