<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | MemberFinance Resource
    |--------------------------------------------------------------------------
    |
    | Translations for the member finance overview page (MemberFinanceResource).
    |
    */

    'navigation_label' => 'Member Finances',
    'label'            => 'Member Finance',
    'plural_label'     => 'Member Finances',

    'infolist' => [
        'section_user'     => 'User information',
        'section_finance'  => 'Account balance',
        'name'             => 'Full name',
        'email'            => 'E-mail',
        'balance'          => 'Current balance',
        'variable_symbol'  => 'Variable symbol for payments',
    ],

    'form' => [
        'section_title'          => 'Member information',
        'name'                   => 'Name',
        'email'                  => 'E-mail',
        'payer_variable_symbol'  => 'Variable symbol',
        'active'                 => 'Active member',
        'balance'                => 'Current balance',
    ],

    'table' => [
        'name'             => 'Name',
        'email'            => 'E-mail',
        'variable_symbol'  => 'Variable symbol',
        'active'           => 'Status',
        'balance'          => 'Balance',
        'status_active'    => 'Active',
        'status_inactive'  => 'Inactive',
    ],

    'filters' => [
        'membership_status'  => 'Membership status',
        'active'             => 'Active',
        'inactive'           => 'Inactive',
        'negative_balance'   => 'Negative balance',
        'zero_balance'       => 'Zero balance',
        'positive_balance'   => 'Positive balance',
    ],

    'actions' => [

        'bulk_credit' => [
            'label'              => 'Add item to selected members',
            'modal_heading'      => 'Bulk add a finance item',
            'modal_description'  => 'The selected amount will be credited to the account of all selected members.',
            'modal_submit'       => 'Add item',
            'field_type'         => 'Item type',
            'notification_title' => 'Bulk operation completed',
            'notification_body'  => 'The finance item was added to :count members.',
        ],

        'deposit' => [
            'label'              => 'Add deposit',
            'modal_heading'      => 'Add a deposit to the member',
            'modal_description'  => 'The selected amount will be credited to the member\'s account as income.',
            'modal_submit'       => 'Add deposit',
            'field_type'         => 'Deposit type',
            'amount_tooltip'     => 'Enter a positive amount. It will be credited to the account.',
            'notification_title' => 'Deposit added',
            'notification_body'  => 'The deposit was successfully credited to the member\'s account.',
            'type_donation'      => 'Extraordinary member deposit',
            'type_initial'       => 'Initial deposit',
            'type_membership'    => 'Membership fee',
        ],

        'export' => [
            'label'           => 'Export to file',
            'col_name'        => 'Name',
            'col_email'       => 'E-mail',
            'col_variable_symbol' => 'Variable symbol',
            'col_balance'     => 'Balance (CZK)',
            'col_active'      => 'Active',
            'active_yes'      => 'Yes',
            'active_no'       => 'No',
        ],

        'deduct' => [
            'label'              => 'Deduct payment',
            'modal_heading'      => 'Deduct a payment from the member',
            'modal_description'  => 'The selected amount will be deducted from the member\'s account.',
            'modal_submit'       => 'Deduct payment',
            'field_type'         => 'Payment type',
            'amount_tooltip'     => 'Enter a positive amount. It will be deducted from the account.',
            'notification_title' => 'Payment deducted',
            'notification_body'  => 'The payment was successfully deducted from the member\'s account.',
            'type_cashout'       => 'Cash out',
            'type_membership'    => 'Membership fee',
            'type_transport'     => 'Travel costs',
        ],

    ],

    'common' => [
        'amount'               => 'Amount (CZK)',
        'amount_tooltip_bulk'  => 'Positive value = income, negative = expense.',
        'note'                 => 'Note',
        'note_optional'        => 'Optional',
    ],

];
