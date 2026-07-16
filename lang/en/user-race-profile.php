<?php

return [

    /*
    |--------------------------------------------------------------------------
    | UserRaceProfile Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings in UserRaceProfile
    | resource.
    |
    */

    'navigation_label' => 'My registrations',
    'label'            => 'My registration',
    'plural_label'     => 'My registrations',

    'list' => [
        'page_title' => 'Registrations',
        'help_label' => 'Help',
    ],

    // Table
    'table' => [
        'reg_number' => 'Registration',
        'first_name' => 'First name',
        'last_name' => 'Last name',
        'street' => 'Street',
        'city' => 'City',
        'zip' => 'ZIP',
        'user-name' => 'User',
        'oris_id' => 'ORIS user ID',
        'si' => 'Chip number',
        'email' => 'Email',
        'phone' => 'Phone',
        'active' => 'Active',
        'active_until' => 'Active until',
        'created_at' => 'Created',
    ],
    'table_filter' => [
        'registrations' => 'Registrations',
        'all_registrations' => 'All registrations',
        'active_registrations' => 'Active registrations',
        'disable_registrations' => 'Inactive registrations',
    ],

    'form' => [
        'section_address'  => 'Address (optional)',
        'section_licence'  => 'Licence',
        'section_user'     => 'User',
        'street'           => 'Street, house number',
        'gender'           => 'Gender',
        'gender_male'      => 'Male',
        'gender_female'    => 'Female',
        'oris_id'          => 'ORIS ID',
        'club_user_id'     => 'Club ORIS ID',
        'user_id_helper'   => 'Automatically assigned to the user',
        'si'               => 'SI chip',
        'si_helper'        => 'Preferred SI chip',
        'licence_ob'       => 'OB licence',
        'licence_lob'      => 'LOB licence',
        'licence_mtbo'     => 'MTBO licence',
        'reg_number_hint'  => '<a href=":help_url" target="_blank">Fill in the registration and click the magnifier.</a>',
    ],

    'common' => [
        'si' => 'SI',
    ],

    'actions' => [

        'search_oris' => [
            'validation_title' => 'Form input',
            'validation_body'  => 'Please fill in the registration number.',
            'notification_title' => 'ORIS API',
            'error_body'       => 'Failed to load the data.',
            'club_error_body'  => 'Failed to load the user\'s club membership data from ORIS.',
            'success_body'     => 'ORIS successfully returned the requested data.',
        ],

        'update_club_oris_id' => [
            'label'                        => 'Update member IDs in ORIS',
            'modal_heading'                => 'Update member IDs in ORIS',
            'modal_description'            => 'Performs a bulk update of member IDs against ORIS, needed for race entries.',
            'modal_submit'                 => 'Update',
            'notification_success_title'   => 'Racer club membership update completed successfully',
            'notification_success_body'    => 'Club membership was updated successfully',
            'notification_error_title'     => 'Something went wrong',
            'notification_error_body'      => 'Something went wrong. You can try to repeat the action or contact the admin describing the error, thank you.',
        ],

        'export' => [
            'label'               => 'Export to Excel',
            'modal_heading'       => 'Creates an export file according to the selection',
            'modal_description'   => 'Choose the desired export. You can choose <strong>all registrations</strong>, deleted or not deleted</br>
                Or only <strong>active</strong> or <strong>inactive</strong>.',
            'modal_submit'        => 'Export',
            'export_type'         => 'Available exports',
            'export_type_all'         => 'All registrations *.xlsx',
            'export_type_active'      => 'Active registrations only *.xlsx',
            'export_type_deactivated' => 'Inactive registrations *.xlsx',
            'notification_title' => 'Registration export completed successfully',
            'notification_body'  => 'Open the Excel file with registered users from your disk.',
        ],

    ],

];
