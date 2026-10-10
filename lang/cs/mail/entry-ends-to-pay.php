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
        'entry_ends_to_pay' => 'Startovné k úhradě – :term termín přihlášek',
    ],

    'club' => [
        'eyebrow' => 'Pro správce plateb · startovné',
        'title' => 'Je čas uhradit startovné.',
        'lead' => 'Končí :term termín přihlášek. Prosím uhraď startovné za přihlášené členy na těchto závodech.',
        'fine' => 'Platební údaje a konkrétní částku ověř v podkladech pořadatele. Tento přehled připomíná uzávěrky.',
    ],

];
