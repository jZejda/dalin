<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | UserAppNotification e-mail
    |--------------------------------------------------------------------------
    |
    | Tělo e-mailu s hromadnou zprávou pro vybrané uživatele. Předmět
    | obsahuje dynamický text zadaný uživatelem a nelokalizuje se.
    |
    */

    'body' => [
        'heading'   => 'Hromadná zpráva',
        'intro'     => 'Upozornění z interního systému :abbr na vybrané uživatele systému.',
        'from_user' => 'Zpráva od uživatele **:name**.',
        'sent_at'   => 'Zpráva zaslána dne: **:date**',
    ],

];
