<?php

declare(strict_types=1);

use App\Enums\MailSource;

return [

    /*
    |--------------------------------------------------------------------------
    | MailLog Resource
    |--------------------------------------------------------------------------
    |
    | Language lines for the sent e-mails overview.
    |
    */

    'navigation_label' => 'Sent e-mails',
    'label' => 'Sent e-mail',
    'plural_label' => 'Sent e-mails',

    'sent_at' => 'Sent at',
    'sent_from' => 'Sent from',
    'sent_until' => 'Sent until',
    'recipient' => 'Recipient',
    'subject' => 'Subject',
    'mailable' => 'E-mail type',
    'mailable_placeholder' => '—',
    'source' => 'Source',
    'source_user' => 'Triggered by user',
    'source_user_placeholder' => '—',

    'source_enum' => [
        MailSource::User->value => 'User',
        MailSource::Cron->value => 'Automatic (cron)',
        MailSource::System->value => 'System',
    ],

];
