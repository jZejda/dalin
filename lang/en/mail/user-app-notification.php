<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | UserAppNotification e-mail
    |--------------------------------------------------------------------------
    |
    | Body of the bulk message e-mail sent to selected users. The subject
    | contains dynamic user-provided text and is not localized.
    |
    */

    'body' => [
        'heading'   => 'Bulk message',
        'intro'     => 'Notification from the internal :abbr system to selected users.',
        'from_user' => 'Message from user **:name**.',
        'sent_at'   => 'Message sent on: **:date**',
    ],

];
