<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Club Settings Page (Config cluster)
    |--------------------------------------------------------------------------
    |
    | Translations for the Club Settings page.
    |
    */

    'navigation_label' => 'Club settings',
    'title' => 'Club settings',

    'form' => [
        'identification' => [
            'section' => 'Club identification',
            'abbr' => 'Club abbreviation',
            'abbr_helper' => 'The abbreviation is used for the ORIS API and the logo path, so it can only be changed in config/site-config.php.',
            'full_name' => 'Full club name',
        ],
        'bank_details' => [
            'section' => 'Bank details',
            'description' => 'These details are shown to members in the credit payment instructions.',
            'primary_bank_account_number' => 'Main account number',
            'primary_bank_account_name' => 'Bank name',
            'iban' => 'IBAN',
            'iban_helper' => 'Used to generate the QR payment. Leave empty to use the value from the configuration file.',
        ],
        'credit' => [
            'section' => 'Credit and membership fees',
            'user_credit_limit' => 'Member credit limit',
            'user_credit_limit_helper' => 'A negative integer. The lowest allowed credit balance — once reached, the member cannot register for a race.',
            'regular_membership_fees_prefix' => 'Variable symbol prefix for regular membership fees',
            'regular_membership_fees_prefix_helper' => 'Digits only.',
            'extra_membership_fees_prefix' => 'Variable symbol prefix for extra fees (credit top-up)',
            'extra_membership_fees_prefix_helper' => 'Warning: used for automatic matching of bank transactions. Payments sent with the old prefix will no longer match after the change.',
        ],
        'contacts' => [
            'section' => 'Contacts',
            'technical_email' => 'Technical e-mail',
            'technical_email_helper' => 'Contact shown in e-mails to members (password reset, credit changes, etc.).',
        ],
    ],

    'notification' => [
        'saved_title' => 'Club settings saved',
    ],

];
