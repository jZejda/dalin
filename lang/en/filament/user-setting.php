<?php

use App\Enums\AppRoles;

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
            'target_users_options' => [
                'all' => 'All active users',
                AppRoles::Member->value => 'Members',
                AppRoles::EventMaster->value => 'Event managers',
                AppRoles::Redactor->value => 'Redactor',
                AppRoles::EventOrganizer->value => 'Event organizer',
                AppRoles::BillingSpecialist->value => 'Billing specialist',
            ],
            'content' => 'Message',
            'notification_title' => 'Email sent',
            'notification_body' => 'An email has been sent to the target users.',
        ],
    ],

];
