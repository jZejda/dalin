<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | AddUpdateSportEvent e-mail
    |--------------------------------------------------------------------------
    |
    | Předmět a tělo e-mailu o změnách v kalendáři závodů.
    |
    */

    'subject' => [
        'add_update_sport_event' => 'Změny v kalendáři závodů',
    ],

    'body' => [
        'heading'             => 'Změny v seznamu závodů',
        'intro'               => 'Vybrané informace o změnách v přihláškovém systému :club.',
        'events_intro'        => 'Do systému byly přidány závody:',
        'table_event'         => 'Závod',
        'table_entry_until'   => 'Přihláška do',
        'divider_heading'     => 'Pokus',
        'unsubscribe_note'    => 'Odhlášení ze zasílání těchto zpráv můžete upravit přímo v klientské sekci v nastavení.',
        'signoff'             => 'Mějte se fajn a jezděte na závody - :club',
    ],

];
