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

    'club' => [
        'eyebrow' => 'Přihlášky · blíží se uzávěrka',
        'title' => 'Ještě stihneš první termín.',
        'lead' => '{1} Za :count den končí první termín přihlášek na následující závody.|[2,4] Za :count dny končí první termín přihlášek na následující závody.|[5,*] Za :count dní končí první termín přihlášek na následující závody.',
        'action' => 'Vybrat závod a přihlásit se',
    ],

];
