<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | EventEntryEnds e-mail
    |--------------------------------------------------------------------------
    |
    | Předmět a tělo e-mailu o blížícím se konci přihlášek závodů.
    |
    */

    'subject' => [
        'event_entry_ends' => 'Blíží se konec přihlášek závodů',
    ],

    'body' => [
        'heading'             => 'Konec přihlášek - 1 termín',
        'intro'               => 'Blíží se konec přihlášek na závody vypsané níže. Do termínu přihlášení zbývají necelé **:days dny**. Přihlášení proveď podle pokynů v administraci.',
        'table_entry_until'   => 'Přihláška do',
        'table_event_name'    => 'Název akce/závodu',
        'table_oris_id'       => 'ORIS ID',
    ],

];
