<?php

return [

    /*
    |--------------------------------------------------------------------------
    | BankAccount Resource
    |--------------------------------------------------------------------------
    |
    | Translations for bank connection management (BankAccountResource).
    |
    */

    'navigation_label' => 'Bank connections',
    'label'            => 'Bank connection',
    'plural_label'     => 'Bank connections',

    'form' => [
        'section_connection'      => 'Connection',
        'name'                    => 'Name',
        'code'                    => 'Bank (connector)',
        'code_helper'             => 'The connector of an existing connection cannot be changed — remove the connection and add a new one.',
        'currency'                => 'Currency',
        'active'                  => 'Connection is active',
        'active_helper'           => 'Transactions are downloaded from active connections only.',
        'section_credentials'     => 'Credentials',
        'credentials_description' => 'Credentials are stored encrypted and are never displayed back. When editing, leave a field empty to keep the stored value.',
        'credential_keep_helper'  => 'Leave empty to keep the stored value.',
    ],

    'table' => [
        'name'         => 'Name',
        'bank'         => 'Bank',
        'currency'     => 'Currency',
        'last_synced'  => 'Last synchronization',
        'never'        => 'never',
        'active'       => 'Active',
    ],

    'credentials' => [
        'token'      => 'API token',
        'account_id' => 'Account ID (account_id)',
    ],

    'list' => [
        'create_action' => 'Add connection',
    ],

];
