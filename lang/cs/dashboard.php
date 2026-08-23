<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dashboard widgets
    |--------------------------------------------------------------------------
    |
    | Překlady pro widgety na hlavním dashboardu (StatsOverview apod.).
    |
    */

    'stats_overview' => [
        'finance_label' => 'Finance',
        'finance_description_positive' => 'Hurá na závody',
        'finance_description_negative' => 'Bylo by fajn zaslat dar',
        'entries_label' => 'Přihlášen do závodů',
        'entries_description' => 'Jsi přihlášen :count ve všech spravovaných profilech.',
        'profiles_label' => 'Závodních profilů',
        'profiles_description' => 'Aktuálně spravuješ závodních profilů',
        'events_label' => 'Závodu v roce :year',
        'events_description' => 'V kalendáři je na tento rok zaneseno závodů',
    ],

    'version' => [
        'heading' => 'Verze aplikace',
        'build' => 'sestavení :build',
        'deployed_at' => 'nasazeno :date',
        'runtime' => 'PHP :php · Laravel :laravel',
        'unknown_build' => 'vývojové sestavení',
    ],

];
