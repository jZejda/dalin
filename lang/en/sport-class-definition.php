<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SportClassDefinition Resource
    |--------------------------------------------------------------------------
    |
    | Translations for managing class definitions (SportClassDefinitionResource).
    |
    */

    'navigation_label' => 'Class definitions',
    'label'            => 'Class definition',
    'plural_label'     => 'Class definitions',

    'form' => [
        'name'         => 'Name',
        'gender'       => 'Gender',
        'gender_female' => 'Female',
        'gender_male'   => 'Male',
        'gender_all'    => 'All',
        'age_from'     => 'Age from:',
        'age_to'       => 'Age to:',
        'sport'        => 'Sport',
        'oris_id'      => 'ORIS ID',
    ],

    'table' => [
        'age_from' => 'Age from',
        'age_to'   => 'Age to',
        'sport'    => 'Sport',
        'oris_id'  => 'ORIS ID',
    ],

    'actions' => [
        'update' => [
            'label'                     => 'Update',
            'modal_heading'             => 'Update class definitions from ORIS',
            'modal_description'         => 'Class definitions according to ORIS. Choose the sport for which you want to update the class definitions.',
            'modal_submit_action_label' => 'Update',
            'sport_event'               => 'Race/event',
            'notification_title'        => 'Class definitions update',
            'notification_body_success' => 'The class definitions were successfully updated from ORIS.',
            'notification_body_error'   => 'Something went wrong. You can try the action again or contact the admin with a description of the error, thank you.',
        ],
    ],

];
