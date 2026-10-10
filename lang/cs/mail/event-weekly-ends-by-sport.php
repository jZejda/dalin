<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | EventWeeklyEndsBySport e-mail
    |--------------------------------------------------------------------------
    |
    | Předmět a tělo e-mailu s týdenním souhrnem termínů přihlášek závodů.
    |
    */

    'subject' => [
        'event_weekly_ends_by_sport' => 'Týdenní souhrn termínů přihlášek',
    ],

    'body' => [
        'heading'              => 'Konec přihlášek',
        'intro'                => 'Týdenní souhrn přihlášek závodů vypsaných níže. Závody jsou rozděleny podle termínu přihlášek v týdnu **:from** - **:to**.',
        'first_term_heading'   => '1. termín přihlášek',
        'first_term_intro'     => 'Závody u kterých končí **první termín** přihlášek.',
        'second_term_heading'  => '2. termín přihlášek',
        'second_term_intro'    => 'Závody u kterých končí **druhý termín** přihlášek.',
        'third_term_heading'   => '3. termín přihlášek',
        'third_term_intro'     => 'Závody u kterých končí **třetí termín** přihlášek.',
        'table_entry_until'    => 'Přihláška do',
        'table_event_date'     => 'Datum akce',
        'table_event_name'     => 'Název akce/závodu',
    ],

    'club' => [
        'eyebrow' => 'Přihlášky · :from – :to',
        'title' => 'Vyber si svůj další závod.',
        'lead' => '{1} Tento týden se uzavírají přihlášky na :count závod. Ať ti neuteče.|[2,4] Tento týden se uzavírají přihlášky na :count závody. Ať ti žádný neuteče.|[5,*] Tento týden se uzavírají přihlášky na :count závodů. Ať ti žádný neuteče.',
        'action' => 'Vybrat závod a přihlásit se',
    ],

];
