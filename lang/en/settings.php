<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Settings Page (Config cluster)
    |--------------------------------------------------------------------------
    |
    | Translations for the Settings page and the parent Configuration cluster.
    |
    */

    'cluster' => [
        'navigation_label' => 'Settings',
        'breadcrumb' => 'Settings',
    ],

    'navigation_label' => 'Settings',
    'title' => 'Settings',

    'form' => [
        'transport' => [
            'section' => 'Transport module',
            'description' => 'Once enabled, races can have a transport type configured, members will see their vehicle management, and will be able to offer carpooling.',
            'toggle_label_enabled' => 'The transport module is enabled',
            'toggle_label_disabled' => 'The transport module is disabled',
            'toggle_helper' => 'Disabling the module hides transport across the whole application; stored data is kept.',
        ],
        'event_payments' => [
            'section' => 'Event payments module',
            'description' => 'Once enabled, the race detail page shows a Payments / Finance tab for managing race profile payments.',
            'toggle_label_enabled' => 'The event payments module is enabled',
            'toggle_label_disabled' => 'The event payments module is disabled',
            'toggle_helper' => 'Disabling the module hides the payments tab on the race detail page; stored data is kept.',
        ],
        'service_orders' => [
            'section' => 'Additional services module',
            'description' => 'Once enabled, members can order additional services (accommodation, overnight stay, etc.) on the race detail page, including payment deadlines. When disabled, only a preview of the offered services is visible.',
            'toggle_label_enabled' => 'The additional services module is enabled',
            'toggle_label_disabled' => 'The additional services module is disabled',
            'toggle_helper' => 'Disabling the module hides the orders tab on the race detail page; stored data is kept.',
        ],
        'marketplace' => [
            'section' => 'Marketplace module',
            'description' => 'Once enabled, members can list product offers on their own behalf or on behalf of the club, and others can order them. Once an offer ends, costs are split according to the ordered quantities.',
            'toggle_label_enabled' => 'The marketplace module is enabled',
            'toggle_label_disabled' => 'The marketplace module is disabled',
            'toggle_helper' => 'Disabling the module hides the marketplace across the whole application; stored data is kept.',
        ],
        'bank' => [
            'section' => 'Bank connection module',
            'description' => 'Once enabled, the Bank statement page and bank connection management will be available. Transactions are downloaded automatically only if the module is enabled and at least one active bank connection exists.',
            'toggle_label_enabled' => 'The bank connection module is enabled',
            'toggle_label_disabled' => 'The bank connection module is disabled',
            'toggle_helper' => 'Disabling the module hides the bank statement and stops downloading transactions; stored data is kept.',
        ],
    ],

    'notification' => [
        'saved_title' => 'Settings saved',
    ],

];
