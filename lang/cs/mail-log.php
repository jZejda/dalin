<?php

declare(strict_types=1);

use App\Enums\MailSource;

return [

    /*
    |--------------------------------------------------------------------------
    | MailLog Resource
    |--------------------------------------------------------------------------
    |
    | Jazykové řetězce pro přehled odeslaných e-mailů.
    |
    */

    'sent_at' => 'Odesláno',
    'sent_from' => 'Odesláno od',
    'sent_until' => 'Odesláno do',
    'recipient' => 'Příjemce',
    'subject' => 'Předmět',
    'mailable' => 'Typ e-mailu',
    'source' => 'Zdroj',
    'source_user' => 'Spustil uživatel',

    'source_enum' => [
        MailSource::User->value => 'Uživatel',
        MailSource::Cron->value => 'Automaticky (cron)',
        MailSource::System->value => 'Systém',
    ],

];
