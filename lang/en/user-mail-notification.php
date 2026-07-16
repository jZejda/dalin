<?php

return [

    /*
    |--------------------------------------------------------------------------
    | User Mail Notification Page
    |--------------------------------------------------------------------------
    |
    | Translations for the user settings page (UserMailNotification).
    |
    */

    'navigation_label' => 'User settings',
    'title'            => 'User settings',

    'breadcrumbs' => [
        'users'    => 'User',
        'settings' => 'Settings',
    ],

    'tabs' => [
        'group_label'      => 'Tabs',
        'mail_settings'    => 'E-mail settings',
        'display_filters'  => 'Display filters',
        'other'            => 'Other',
        'api_key'          => 'API Key',
        'calendar'         => 'Calendar',
    ],

    'sections' => [
        'news' => [
            'heading'     => 'News notifications',
            'description' => 'Here you can set up notifications for public news and news from the members section.',
        ],
        'entry_deadline' => [
            'heading'     => 'Notifications about the approaching end of race entries',
            'description' => 'If the entry deadline for races is approaching, you will be notified by e-mail at the specified time, in advance according to the number of days before the entry deadline that you set.',
        ],
        'weekly_summary' => [
            'heading'     => 'Summary of races whose entry deadline ends next week',
            'description' => 'In the settings you define which sports will be summarized in the e-mail. The summary contains races whose entry deadline ends next week.',
        ],
        'other_mails' => [
            'heading'     => 'Other e-mails',
            'description' => 'The pre-race summary e-mail contains information about the event, the start times of entered racers and the parameters of their classes.',
        ],
        'sign_up_permissions' => [
            'heading'     => 'Entry permissions',
            'description' => 'In the settings you can grant selected users the right to enter and withdraw all registrations you manage. Suitable for example for family members or friends. The right can be revoked at any time.',
        ],
        'api_key' => [
            'heading'     => 'API key management',
            'description' => 'The API key is used for authentication when using the API. Keep it secret.',
        ],
        'calendar' => [
            'heading'     => 'Calendar feeds',
            'description' => 'Public feeds are available without logging in. Personal feeds require generating a token and show only your races and trainings. Feeds can be added to Google Calendar, Apple Calendar and other applications supporting iCal.',
        ],
    ],

    'form' => [
        'news'                         => 'News',
        'news_option_public'           => 'Public news',
        'news_option_members'          => 'Members section news',
        'sport'                        => 'Sport',
        'sport_time_trigger'           => 'Approximate notification hour',
        'days_before_event_entry_ends' => 'Days before entry deadline',
        'week_report_by_sport'         => 'Sport',
        'pre_race_summary_enabled'     => 'Pre-race summary',
        'pre_race_summary_days_before' => 'Days before the race',
        'pre_race_summary_time_trigger' => 'Approximate sending hour',
        'event_filters'                => 'User filters for the race and event list.',
        'event_filters_add_action'     => 'Add new filter',
        'filter_name'                  => 'Name',
        'filter_name_hint'             => 'Will be shown as the filter title.',
        'filter_sport_list'            => 'Sport',
        'filter_sport_event_type'      => 'Event type',
        'filter_icon'                  => 'Icon',
        'users_allow_sign_up_for_race' => 'Users who can sign me up and withdraw me from races',
    ],

    'common' => [
        'copied_title' => 'Copied',
        'error_title'  => 'Error',
    ],

    'actions' => [
        'generate_api_key' => [
            'notification_title' => 'API key generated',
            'notification_body'  => 'A new API key has been successfully generated. The key hash is shown below and remains visible even after the page is refreshed.',
        ],
        'regenerate_api_key' => [
            'notification_title' => 'API key regenerated',
            'notification_body'  => 'The API key has been successfully regenerated. The old key is no longer valid. The new key hash is shown below.',
        ],
        'delete_api_key' => [
            'notification_title' => 'API key deleted',
            'notification_body'  => 'The API key has been successfully deleted.',
        ],
        'copy_api_key' => [
            'notification_body' => 'The API key has been copied to the clipboard.',
        ],
        'generate_calendar_token' => [
            'notification_title' => 'Calendar token generated',
            'notification_body'  => 'A new token has been successfully generated.',
        ],
        'regenerate_calendar_token' => [
            'notification_title' => 'Calendar token regenerated',
            'notification_body'  => 'The old token has been invalidated. A new token has been successfully generated.',
        ],
        'revoke_calendar_token' => [
            'notification_title' => 'Calendar token revoked',
            'notification_body'  => 'The token has been successfully revoked.',
        ],
        'copy_calendar_token' => [
            'notification_body' => 'The calendar token has been copied to the clipboard.',
        ],
        'copy_calendar_url' => [
            'notification_body' => 'The calendar feed address has been copied to the clipboard.',
        ],
        'copy_calendar_url_failed' => [
            'notification_body' => 'The address could not be copied to the clipboard.',
        ],
        'submit' => [
            'notification_title' => 'Settings saved',
            'notification_body'  => 'The settings changes have been saved.',
        ],
    ],

];
