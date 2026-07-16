<?php

use App\Enums\SportEventExportsType;

return [

    /*
    |--------------------------------------------------------------------------
    | SportEventExport Resource
    |--------------------------------------------------------------------------
    |
    | Translations for managing race organization export outputs
    | (SportEventExportResource).
    |
    */

    'navigation_label' => 'Organization exports',
    'label'            => 'Organization export',
    'plural_label'     => 'Organization exports',

    'type_enum' => [
        SportEventExportsType::EventEntryListCat->value => 'Category start list',
        SportEventExportsType::ResultEntryListCat->value => 'Category results',
    ],

    'aside_link_title_enum' => [
        SportEventExportsType::EventEntryListCat->value => 'Start list',
        SportEventExportsType::ResultEntryListCat->value => 'Results',
    ],

    'form' => [
        'export_type'         => 'Export type',
        'file_type'           => 'File type',
        'sport_event'         => 'Event ID',
        'start_time'          => 'Time 00',
        'sport_event_leg_id'  => 'Event leg ID',
    ],

    'table' => [
        'path'        => 'Path',
        'result_path' => 'File path',
    ],

];
