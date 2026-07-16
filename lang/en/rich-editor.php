<?php

return [

    /*
    |--------------------------------------------------------------------------
    | RichEditor custom blocks
    |--------------------------------------------------------------------------
    |
    | Translations for the RichEditor custom blocks (RichContentCustomBlocks)
    | — headings, form labels and preview texts.
    |
    */

    'blocks' => [

        'hero' => [
            'label' => 'Hero',
            'modal_heading' => 'Hero block configuration',
            'modal_description' => 'Set the parameters for the hero section',
            'heading' => 'Main heading',
            'heading_placeholder' => 'Enter the main heading',
            'subheading' => 'Subheading',
            'subheading_placeholder' => 'Enter the subheading (optional)',
            'button_label' => 'Button text',
            'button_label_placeholder' => 'Enter the button text (optional)',
            'button_url' => 'Button URL',
            'button_url_placeholder' => 'Enter the button URL (optional)',
            'preview_untitled' => 'Unnamed hero block',
            'preview_label' => 'Hero section: :heading',
        ],

        'alert' => [
            'label' => 'Alert',
            'modal_heading' => 'Alert block configuration',
            'modal_description' => 'Set the parameters for the alert section',
            'type' => 'Alert type',
            'type_options' => [
                'default' => 'Default',
                'info' => 'Information',
                'warning' => 'Warning',
                'error' => 'Error',
            ],
            'heading' => 'Heading',
            'heading_placeholder' => 'Enter the alert heading',
            'content' => 'Content (Markdown)',
            'content_placeholder' => 'Enter the content in Markdown format',
            'preview_untitled' => 'Unnamed alert',
            'preview_label' => 'Alert (:type): :heading',
        ],

        'table' => [
            'label' => 'Table',
            'modal_heading' => 'Table configuration',
            'modal_description' => 'Paste tab-separated data (e.g. copied from Excel, ORIS or a PDF). Columns can also be separated by two or more spaces.',
            'title' => 'Heading (optional)',
            'raw' => 'Table data',
            'raw_placeholder' => "cat\tlength\tclimb\tcontrols\nD10\t2.6\t65\t9\nD12\t2.8\t65\t10",
            'raw_helper' => 'Each line = one table row. Separate columns with a tab or two or more spaces.',
            'has_header' => 'First row is a header',
            'striped' => 'Striped rows',
            'compact' => 'Compact (smaller padding)',
            'preview_label_titled' => 'Table: :title (:count rows)',
            'preview_label_untitled' => 'Table (:count rows)',
        ],

        'content_divider' => [
            'label' => 'Section divider',
            'modal_heading' => 'Section divider configuration',
            'modal_description' => 'Set the parameters for the section divider',
            'separator' => 'Divider',
            'separator_placeholder' => 'E.g. News, About the club, Races...',
            'heading' => 'Main heading',
            'heading_placeholder' => 'Enter the main heading of the section',
            'preview_untitled' => 'Unnamed divider',
            'preview_label' => 'Divider: :heading',
        ],

        'simple_divider' => [
            'label' => 'Simple divider',
            'modal_heading' => 'Simple divider configuration',
            'modal_description' => 'Set the lantern number and heading',
            'number' => 'Lantern number',
            'number_placeholder' => 'E.g. 31',
            'heading' => 'Heading',
            'heading_placeholder' => 'Enter the heading',
            'preview_untitled' => 'Unnamed divider',
            'preview_label' => 'Lantern #:number: :heading',
        ],

    ],

];
