<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | UserEntryNotification e-mail
    |--------------------------------------------------------------------------
    |
    | Tělo e-mailu se zprávou pro přihlášené závodníky. Předmět obsahuje
    | dynamický text zadaný uživatelem a nelokalizuje se.
    |
    */

    'body' => [
        'heading'    => 'Notifikace k závodu',
        'intro'      => 'Zpráva k závodu **:name**:alt_name odeslána na všechny aktuálně přihlášené závodníky.',
        'date_line'  => 'Datum: **:date**',
        'place_line' => 'Místo: **:place**',
    ],

];
