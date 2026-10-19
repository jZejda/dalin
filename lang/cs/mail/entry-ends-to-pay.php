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
        'fine_with_payments' => 'Částky jsou podle vyúčtování klubu v ORIS v uvedený čas – přihlášky se ještě mohou do konce uzávěrky změnit. U závodů bez platebních údajů ověř platbu v podkladech pořadatele.',
    ],

    'payment' => [
        'to_pay' => 'K úhradě',
        'paid' => 'Podle ORIS uhrazeno (:amount)',
        'breakdown' => 'startovné :entry · čipy :si · služby :service · už uhrazeno :paid',
        'bank_account' => 'Účet',
        'vs' => 'VS',
        'fetched_at' => 'Stav v ORIS :time',
        'qr_alt' => 'QR platba',
        'qr_caption' => 'QR platba',
    ],

];
