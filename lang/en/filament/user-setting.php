<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UserSettings page
    |--------------------------------------------------------------------------
    |
    | Translations for the My overview page (UserSettings) and related
    | actions (sending mail, changing password).
    |
    */

    'heading' => 'My overview',
    'changelog_note' => 'The dalin app changelog is available in the documentation.',

    'actions' => [
        'send_mail' => [
            'label' => 'Send email',
            'modal_heading' => 'Sends an email to selected groups of system users.',
            'modal_description' => 'The email is sent from the queue every 5 minutes.',
            'modal_submit' => 'Send',
            'subject' => 'Message subject',
            'reply_to' => 'Reply-to address',
            'target_users' => 'Target group of users by role',
            'target_users_helper' => 'Only active users receive the email. Anyone in several selected groups gets it just once.',
            'target_users_all' => 'All active users',
            'content' => 'Message',
            'notification_title' => 'Email sent',
            'notification_body' => '{1} The email has been queued for :count user.|[2,*] The email has been queued for :count users.',
            'notification_empty_title' => 'Email not sent',
            'notification_empty_body' => 'No active user matches the selected groups. Check the target group selection.',
        ],
    ],

];
