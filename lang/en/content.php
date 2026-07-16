<?php

use App\Enums\ContentFormat;
use App\Enums\PageStatus;
use App\Enums\PostStatus;

return [

    /*
    |--------------------------------------------------------------------------
    | Content module (Pages, Posts, ContentCategories)
    |--------------------------------------------------------------------------
    |
    | Translations for content management - pages (PageResource), news posts
    | (PostResource) and content categories (ContentCategoryResource).
    |
    */

    'enums' => [
        'content_format' => [
            ContentFormat::Html->value => 'HTML',
            ContentFormat::Markdown->value => 'Markdown',
            ContentFormat::TipTapJson->value => 'TipTap JSON',
        ],

        'page_status' => [
            PageStatus::Open->value => 'Published',
            PageStatus::Closed->value => 'Closed',
            PageStatus::Draft->value => 'Draft',
            PageStatus::Archive->value => 'Archived',
        ],

        'post_status' => [
            PostStatus::Public->value => 'Public',
            PostStatus::Private->value => 'Internal',
        ],
    ],

    'page' => [
        'navigation_label' => 'Pages',
        'label'            => 'Page',
        'plural_label'     => 'Pages',

        'form' => [
            'content'          => 'Content',
            'author'           => 'Author',
            'format'           => 'Format',
            'category'         => 'Category',
            'show_category_menu' => 'Show category menu?',
            'weight'           => 'Weight',
            'meta'             => 'Meta',
            'meta_key'         => 'Key',
            'meta_value'       => 'Value',
        ],

        'table' => [
            'title'  => 'Title',
            'author' => 'Author',
            'format' => 'Content format',
        ],

        'search' => [
            'author'   => 'Author',
            'category' => 'Category',
        ],

        'relation' => [
            'title' => 'Page(s)',
        ],
    ],

    'post' => [
        'navigation_label' => 'News',
        'label'            => 'News post',
        'plural_label'     => 'News',

        'form' => [
            'title'                    => 'Title',
            'content'                  => 'News content',
            'section_additional'       => 'Additional information',
            'section_additional_description' => 'Editorial summary of the news - optional - available after expanding',
            'private'                  => 'Internal news',
            'author'                   => 'Author',
            'format'                   => 'Format',
        ],

        'table' => [
            'author' => 'Author',
            'format' => 'Content format',
        ],

        'search' => [
            'author'   => 'Author',
            'status'   => 'Status',
            'private'  => 'Private',
            'public'   => 'Public',
            'not_available' => 'N/A',
        ],

        'actions' => [
            'send_news_email' => [
                'label'                     => 'Send e-mail',
                'modal_heading'             => 'Send a news e-mail',
                'modal_description'        => 'The e-mail is sent separately to each user. If you choose to send it to everyone, it will be sent regardless of user preferences.',
                'modal_submit_action_label' => 'Send',
                'notification_title'       => 'News e-mail sent',
                'notification_body'        => 'The e-mail was sent to the selected users.',
                'subject'                   => 'Message subject',
                'selection'                 => 'Choose an option',
                'selection_interested'      => 'Users who are interested in news',
                'selection_all'             => 'All active users of the system',
            ],
        ],
    ],

    'category' => [
        'search' => [
            'slug'        => 'Slug',
            'pages_count' => 'Number of posts',
        ],
    ],

];
