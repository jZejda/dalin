<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | PreRaceSummaryMail e-mail
    |--------------------------------------------------------------------------
    |
    | Subject and body of the pre-race summary e-mail.
    |
    */

    'subject' => [
        'pre_race_summary' => 'Pre-race summary: :event',
    ],

    'body' => [
        'heading'            => 'Pre-race summary: :event',
        'info_heading'       => 'Event information',
        'date_label'         => 'Date',
        'days_count'         => '{1} :count day|[2,*] :count days',
        'start_time_label'   => 'First stage start',
        'oris_label'         => 'ORIS',
        'oris_link_text'     => 'Race :id',
        'profiles_heading'   => 'Race profiles',
        'reg_number_label'   => 'Reg. number',
        'name_label'         => 'Name',
        'category_label'     => 'Category',
        'plus_min_label'     => '+min',
        'distance_label'     => 'Distance',
        'controls_label'     => 'Controls',
        'climbing_label'     => 'Climb',
        'description_heading' => 'Event description',
        'links_heading'      => 'Links',
        'link_name_label'    => 'Name',
        'link_url_label'     => 'Link',
        'news_heading'       => 'News',
    ],

];
