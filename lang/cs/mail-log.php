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

    'navigation_label' => 'Odeslané e-maily',
    'label' => 'Odeslaný e-mail',
    'plural_label' => 'Odeslané e-maily',

    'sent_at' => 'Odesláno',
    'sent_from' => 'Odesláno od',
    'sent_until' => 'Odesláno do',
    'recipient' => 'Příjemce',
    'subject' => 'Předmět',
    'mailable' => 'Typ e-mailu',
    'mailable_placeholder' => '—',
    'source' => 'Zdroj',
    'source_user' => 'Spustil uživatel',
    'source_user_placeholder' => '—',

    'source_enum' => [
        MailSource::User->value => 'Uživatel',
        MailSource::Cron->value => 'Automaticky (cron)',
        MailSource::System->value => 'Systém',
    ],

];
