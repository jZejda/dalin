<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Settings Page (Config cluster)
    |--------------------------------------------------------------------------
    |
    | Jazykové řetězce pro stránku Nastavení a nadřazený cluster Konfigurace.
    |
    */

    'cluster' => [
        'navigation_label' => 'Konfigurace',
        'breadcrumb' => 'Konfigurace',
    ],

    'navigation_label' => 'Nastavení',
    'title' => 'Nastavení',

    'form' => [
        'transport' => [
            'section' => 'Modul Doprava',
            'description' => 'Po zapnutí modulu bude možné u závodů nastavit typ dopravy, členové uvidí správu svých vozidel a budou moci nabízet spolujízdu.',
            'toggle_label' => 'Modul doprava je zapnutý',
            'toggle_helper' => 'Vypnutí modulu skryje dopravu v celé aplikaci, uložená data zůstanou zachována.',
        ],
        'event_payments' => [
            'section' => 'Modul Platby u závodů',
            'description' => 'Po zapnutí modulu se na detailu závodu zobrazí záložka Platby / Finance se správou plateb závodních profilů.',
            'toggle_label' => 'Modul plateb u závodů je zapnutý',
            'toggle_helper' => 'Vypnutí modulu skryje záložku plateb na detailu závodu, uložená data zůstanou zachována.',
        ],
        'service_orders' => [
            'section' => 'Modul Doplňkové služby',
            'description' => 'Po zapnutí modulu si členové mohou na detailu závodu objednávat doplňkové služby (ubytování, nocleh apod.) včetně termínů plateb. Při vypnutém modulu je vidět jen náhled nabízených služeb.',
            'toggle_label' => 'Modul doplňkových služeb je zapnutý',
            'toggle_helper' => 'Vypnutí modulu skryje záložku objednávek na detailu závodu, uložená data zůstanou zachována.',
        ],
        'marketplace' => [
            'section' => 'Modul Tržiště',
            'description' => 'Po zapnutí modulu mohou členové vystavovat nabídky produktů za sebe nebo za oddíl a ostatní si je objednávat. Po ukončení nabídky se náklady rozúčtují podle objednaných kusů.',
            'toggle_label' => 'Modul tržiště je zapnutý',
            'toggle_helper' => 'Vypnutí modulu skryje tržiště v celé aplikaci, uložená data zůstanou zachována.',
        ],
        'bank' => [
            'section' => 'Modul Napojení na banku',
            'description' => 'Po zapnutí modulu bude dostupná stránka Bankovní výpis a správa bankovních napojení. Transakce se automaticky stahují, jen pokud je modul zapnutý a existuje alespoň jedno aktivní bankovní napojení.',
            'toggle_label' => 'Modul napojení na banku je zapnutý',
            'toggle_helper' => 'Vypnutí modulu skryje bankovní výpis a zastaví stahování transakcí, uložená data zůstanou zachována.',
        ],
    ],

    'notification' => [
        'saved_title' => 'Nastavení uloženo',
    ],

];
