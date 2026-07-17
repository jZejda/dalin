<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | EntryEndsToPay e-mail
    |--------------------------------------------------------------------------
    |
    | Předmět a tělo e-mailu o konci termínu přihlášek k závodu.
    |
    */

    'subject' => [
        'entry_ends_to_pay' => 'Zaslání platby k :deadline termínu závodů',
    ],

    'body' => [
        'heading'             => 'Konec přihlášek závodu',
        'intro'               => 'Aktuálně končí **:deadline termín** přihlášek k závodu vypsaných níže. Prosím o uhrazení startovného přihlášených členů.',
        'deadline_heading'    => ':deadline termín přihlášek',
        'deadline_text'       => 'Závody u kterých právě končí **:deadline termín** přihlášek.',
        'table_entry_until'   => 'Přihláška do',
        'table_event_name'    => 'Název akce/závodu',
        'table_oris_id'       => 'ORIS ID',
    ],

];
