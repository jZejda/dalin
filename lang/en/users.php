<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings in User
    | resource.
    |
    */

    'navigation_label' => 'Users',
    'label'            => 'User',
    'plural_label'     => 'Users',

    'form' => [
        'payer_variable_symbol'        => 'User variable symbol',
        'payer_variable_symbol_helper' => 'This should be the first digits of the registration number, exactly 4 digits.',
        'password'                     => 'Password',
        'new_password'                 => 'New password',
        'roles'                        => 'Roles',
        'roles_warning'                => '**Info:** The user must be assigned at least one role, otherwise they will not have permission for any action.',
    ],

    // Table
    'table' => [
        'name'            => 'Name',
        'email'           => 'Email',
        'variable_symbol' => 'VS',
        'roles'           => 'Roles',
        'created_at'      => 'Created',
        'updated_at'      => 'Updated',
    ],

    'table_filter' => [
        'users' => 'Users',
        'all_users' => 'All users',
        'active_users' => 'Active users',
        'disable_users' => 'Inactive users',
    ],

    'actions' => [

        'reset_password' => [
            'label'               => 'Reset password',
            'modal_heading'       => 'New password',
            'modal_description'   => 'Resets the user\'s password.<br><br> After confirming, the user: :user <strong>will be sent an email with the new password.</strong>',
            'field_password'      => 'New password',
            'notification_title'  => 'Password reset',
            'notification_body'   => 'A new password has been reset and sent to the user\'s email address: :email.',
        ],

        'change_status' => [
            'label'                  => 'Change status',
            'modal_heading'          => 'Change user status',
            'modal_description'      => 'The current status of user :user is <strong>:status</strong>.<br>Do you really want to change their status?',
            'field_status'           => 'User status',
            'status_active'          => 'Active',
            'status_inactive'        => 'Inactive',
            'current_status_active'  => 'active',
            'current_status_inactive' => 'inactive',
            'activated'              => 'activated',
            'deactivated'            => 'deactivated',
            'notification_title'     => 'User status change',
            'notification_body'      => 'User :user has been :status.',
        ],

    ],

    'user_credit_relation' => [
        'label'          => 'Finances',
        'plural_label'   => 'Finances',
        'title'          => 'Finances',
        'table' => [
            'registration'      => 'Racer registration',
            'amount_total'      => 'Total',
            'comments'          => 'Comments',
            'record_id'         => 'id: :id',
            'event_internal_id' => 'internal event id: :id',
        ],
        'filters' => [
            'sport_event' => 'Race',
            'created_from' => 'Date from',
            'created_until' => 'Date to',
        ],
        'actions' => [
            'export' => [
                'label' => 'Export user finances',
                'col_created_at' => 'Created on',
                'col_event_name' => 'Event name',
                'col_event_alt_name' => 'Alternative name',
                'col_reg_number' => 'Registration',
                'col_amount' => 'Amount',
                'col_source_user' => 'Entered by',
            ],
        ],
    ],

    'race_profile_relation' => [
        'label' => 'Race profile',
        'title' => 'Race profile',
    ],

];
