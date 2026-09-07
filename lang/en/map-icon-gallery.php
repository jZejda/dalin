<?php

return [

    'navigation_label' => 'Map icons',
    'title' => 'Map icon gallery',
    'description' => 'Live preview of every icon used on the race map — sport colors are read straight from the database, so this page always stays up to date. See docs/map-icons.md for the full system description.',

    'sports_section' => 'Sports',
    'sports_section_description' => 'Color and icon come from sport_lists.color. Bottom-right modifier: E = stage race, R = relay.',
    'no_sports_yet' => 'There is no sport in the database yet (sport_lists table).',

    'modifier_none' => 'no modifier',
    'modifier_e' => 'stages (E)',
    'modifier_r' => 'relay (R)',

    'categories_section' => 'Event categories',
    'categories_section_description' => 'Top-right badge based on SportEventType. Race has no badge — that\'s the default, clean look.',

    'auxiliary_section' => 'Auxiliary points',
    'auxiliary_section_description' => 'Neutral gray icon for points of interest (SportEventMarker) that do not represent the race itself.',

    'color_label' => 'Color',
];
