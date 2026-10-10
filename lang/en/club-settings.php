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
        'branding' => [
            'section' => 'Club colours',
            'description' => 'Club accent colour used in e-mails (header stripe, main button, highlighted deadlines). The button text colour and light tints are derived automatically.',
            'accent_color' => 'Accent colour',
            'accent_color_helper' => 'Colour in #rrggbb format. When left empty, the default :default is used. The logo and social links in e-mails come from the Public website and social networks section.',
        ],
        'seo' => [
            'section' => 'Public website and social networks',
            'description' => 'Data for search engines (SEO) and previews of links shared on social networks.',
            'seo_description' => 'Club description',
            'seo_description_helper' => 'One or two sentences about the club (ideally up to 160 characters). Shown in search results and link previews for pages without their own description.',
            'seo_image' => 'Default sharing image',
            'seo_image_helper' => 'JPG or PNG sized 1200 × 630 px (e.g. the logo on the club colours). Used in link previews on Facebook, WhatsApp etc. for pages without their own image.',
            'seo_logo' => 'Club logo',
            'seo_logo_helper' => 'Square PNG or JPG, at least 112 × 112 px. Search engines may show it next to the club\'s results; it is also shown in the e-mail header (PNG recommended).',
            'seo_same_as' => 'Social network profiles',
            'seo_same_as_helper' => 'Full URLs of the club profiles (Facebook, Instagram, Strava…). Confirm each with Enter.',
        ],
    ],

    'notification' => [
        'saved_title' => 'Club settings saved',
    ],

];
