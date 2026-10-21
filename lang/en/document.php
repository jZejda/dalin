<?php

declare(strict_types=1);

return [
    'navigation_label' => 'Documents',
    'label'            => 'Document',
    'plural_label'     => 'Documents',

    'form' => [
        'title'               => 'Title',
        'slug'                => 'Address',
        'slug_helper'         => 'Permanent address of the document. Uploading a new file later keeps the link the same. Avoid changing it once published, old links would stop working.',
        'description'         => 'Description',
        'description_helper'  => 'Optional. A short sentence under the title on the public page.',
        'file'                => 'File',
        'file_helper'         => 'PDF, Word, Excel, OpenDocument or an image, max. :max MB. A new file replaces the current one.',
        'category'            => 'Category',
        'year'                => 'Year',
        'year_helper'         => 'The year the document refers to (e.g. the 2025 annual report). Can stay empty for statutes.',
        'is_public'           => 'Public document',
        'is_public_helper'    => 'Off: only logged-in members can see and download the document, and search engines never get it.',
        'sort'                => 'Order',
        'sort_helper'         => 'Lower numbers come first. Documents are sorted by year (newest first), then by this order.',
    ],

    'table' => [
        'title'             => 'Title',
        'category'          => 'Category',
        'year'              => 'Year',
        'is_public'         => 'Public',
        'size'              => 'Size',
        'uploaded_by'       => 'Uploaded by',
        'updated_at'        => 'Updated',
        'empty_heading'     => 'No documents yet',
        'empty_description' => 'Upload an annual report, the statutes or meeting minutes. Public documents appear on the Documents page.',
    ],

    'filters' => [
        'category'  => 'Category',
        'is_public' => 'Public',
    ],

    'actions' => [
        'open' => [
            'label' => 'Open file',
        ],
        'public_list' => [
            'label' => 'Public page',
        ],
    ],

    'enums' => [
        'category' => [
            'annual_report' => 'Annual reports',
            'statutes'      => 'Statutes and regulations',
            'minutes'       => 'Meeting minutes',
            'other'         => 'Other',
        ],
    ],

    'public' => [
        'title'         => 'Documents',
        'description'   => 'Annual reports, statutes and meeting minutes of :club to download.',
        'lead'          => 'Annual reports, statutes and other club documents to download.',
        'empty'         => 'There are no documents yet.',
        'members_only'  => 'Members only',
        'members_hint'  => 'Internal documents such as meeting minutes are visible to members after logging in.',
        'members_login' => 'Log in',
    ],
];
