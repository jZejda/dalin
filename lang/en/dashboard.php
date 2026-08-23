<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard widgets
    |--------------------------------------------------------------------------
    |
    | Translations for the main dashboard widgets (StatsOverview etc.).
    |
    */

    'stats_overview' => [
        'finance_label' => 'Finance',
        'finance_description_positive' => 'Off to the races',
        'finance_description_negative' => 'A donation would be nice',
        'entries_label' => 'Entered races',
        'entries_description' => 'You are entered :count times across all managed profiles.',
        'profiles_label' => 'Race profiles',
        'profiles_description' => 'You currently manage this many race profiles',
        'events_label' => 'Races in :year',
        'events_description' => 'This many races are listed in the calendar for this year',
    ],

    'version' => [
        'heading' => 'Application version',
        'build' => 'build :build',
        'deployed_at' => 'deployed :date',
        'runtime' => 'PHP :php · Laravel :laravel',
        'unknown_build' => 'development build',
    ],

];
