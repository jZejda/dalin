<?php

use App\Enums\UserCreditStatus;

return [

    /*
    |--------------------------------------------------------------------------
    | UserCredit Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings in UserCredit
    | resource.
    |
    */

    'navigation_label' => 'Event billing',
    'label' => 'Event billing',
    'plural_label' => 'Event billing',

    'id' => '#ID',
    'user' => 'Add to user',
    'user_profile' => 'Race profile',
    'related_user_profile' => 'Transfer in favour of user',
    'user_source_id' => 'Created/changed by',
    'event_name' => 'Race/event',
    'status' => 'Status',
    'amount' => 'Amount',
    'currency' => 'Currency',
    'note' => 'Note',

    // Credit Resource
    'form' => [
        'type_title' => 'Entry type',
        'price_title' => 'Price',
        'amount_title' => 'Amount',
        'currency_title' => 'Currency',
        'source_title' => 'Entered/edited by',
        'related_user_hint' => 'Required if a transfer between users is selected.',
        'warning_tooltip' => 'Warning',
    ],
    'credit_type_enum' => [
        'in' => 'Deposit',
        'out' => 'Withdrawal',
        'donation' => 'Donation',
    ],

    'credit_status_enum' => [
        UserCreditStatus::Done->value => 'Done',
        UserCreditStatus::UnAssign->value => 'Unassigned',
        UserCreditStatus::Open->value => 'Open',
    ],

    'credit_source_enum' => [
        'user' => 'User',
        'system' => 'System',
        'cron' => 'System',
    ],

    // Table
    'table' => [
        'created_at_title' => 'Transaction',
        'sport_event_date' => 'Event date',
        'sport_event_title' => 'Race/Event',
        'amount_title' => 'Amount',
        'source_user_title' => 'Entered by',
        'for_user' => 'To user:  ',
        'from_user' => 'From user:  ',
        'registration' => 'Registration',
        'event_internal_id' => 'internal event id: :id',
        'comments' => 'Comments',
        'transaction_link' => 'Transaction: :id',
    ],

    // Filters
    'filters' => [
        'sport_event' => 'Race',
        'unassigned_racer' => 'No racer assigned',
    ],

    // Actions
    'actions' => [
        'transport_billing' => [
            'action_group_label' => 'Travel billing',
            'modal_description' => 'Billing of travel costs between club members. This transfers funds between club members
            in order to settle travel costs. The action creates two records, one positive for the user to whom the amount is credited.
            The other for the one from whom the amount is deducted.',
            'modal_heading' => 'Travel cost billing.',
            'modal_submit_action_label' => 'Add billing',

        ],
        'transfer_between_users_billing' => [
            'action_group_label' => 'Redistribution between members',
            'modal_description' => 'This transfers funds between club members, for example when one member paid the entry fee for another member.
            The action creates two records, one positive for the user to whom the amount is credited.
            The other for the one from whom the amount is deducted.',
            'modal_heading' => 'Redistribution of funds between members',
        ],
        'transfer_billing_common' => [
            'default_text' => 'Transfer of funds between members',
            'credit_to_user' => 'Credit the amount to user',
            'credit_to_user_hint' => 'The amount will be credited to this user\'s account.',
            'credit_from_user' => 'Deduct the amount from user',
            'credit_from_user_hint' => 'The chosen amount will be deducted from this user.',
            'amount_positive_hint' => 'Only positive values can be entered.',
            'sport_event_not_required_hint' => 'No need to fill in.',
            'notification_title' => 'Travel billing',
            'notification_body' => 'The travel billing has been assigned to the users',
        ],
        'add_note' => [
            'label' => 'Note',
            'modal_heading' => 'Note on the payment',
            'modal_description' => 'If something is not right, please write here the reasons why it is different. Please be brief and factual.',
            'modal_submit_action_label' => 'Save note',
            'note_label' => 'Note',
            'notification_saved_title' => 'The note has been saved',
            'notification_saved_body' => 'Thank you for sending your query about the billing, we will try to resolve it.',
            'notification_billing_title' => 'Note on billing',
            'notification_billing_body' => 'User: :user | Billing ID: :id',
        ],
        'oris_balance' => [
            'label' => 'Load billing from ORIS',
            'modal_heading' => 'Downloads the billing from the ORIS event',
            'modal_description' => 'Choose the event and load the billing. You can repeat the action.',
            'modal_submit_action_label' => 'Download billing',
            'sport_event' => 'Race/event',
            'badge_billed' => 'Billed',
            'badge_pending' => 'Pending',
        ],
    ],

    'list' => [
        'navigation_label' => 'My credit',
        'page_title' => 'Finance',
        'new_billing_label' => 'New billing',
        'new_record_label' => 'New record',
    ],

    'widgets' => [
        'header' => [
            'balance_label' => 'Current balance',
            'description_positive' => 'Off to the races',
            'description_negative' => 'A donation would be nice',
        ],
        'stats' => [
            'unassigned_amount' => 'Unassigned transactions Kč',
            'unassigned_count' => 'Number of unassigned',
        ],
    ],
];
