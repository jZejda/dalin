<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | SendSportEventNearestMail e-mail
    |--------------------------------------------------------------------------
    |
    | Předmět a tělo e-mailu o blížícím se konci přihlášek na závody.
    |
    */

    'subject' => [
        'nearest' => 'Blížící se konec přihlášek',
    ],

    'body' => [
        'heading'             => 'Konec přihlášek - 1 termín',
        'intro'               => 'Blíží se konec přihlášek na závody vypsané níže. Do termínu přihlášení zbývají necelé **dva dny**. Přihlášení proveď podle pokynů v administraci.',
        'table_entry_until'   => 'Přihláška do',
        'table_event_name'    => 'Název akce/závodu',
        'signoff'             => 'Mějte se fajn a jezděte na závody - :club',
        'unsubscribe_note'    => 'Odhlášení ze zasílání těchto zpráv můžete upravit přímo v klientské sekci v nastavení.',
    ],

];
