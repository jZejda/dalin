<?php

return [

    'sport' => [
        0 => 'All',
        1 => 'Foot-O',
        2 => 'Ski-O',
        3 => 'MTBO',
        4 => 'TRAIL',
    ],

    'type' => [
        0 => 'All',
        1 => 'SI contact',
        2 => 'SI contactless',
        3 => 'SI contactless BS11',
        4 => 'manual',
        5 => 'ToePunch',
    ],

    'form' => [
        'si' => 'Chip number',
        'sport' => 'Sport',
        'type' => 'Chip type',
        'extra_chips' => 'Additional chips',
        'extra_chips_helper' => 'Up to two additional chips, e.g. for another sport. An entry is pre-filled with the chip matching the event sport, otherwise the default one.',
        'add_chip' => 'Add chip',
    ],

    'table' => [
        'extra_chips' => 'Additional chips',
    ],

    'status' => [
        'in_sync' => 'In sync',
        'differs' => 'Differs',
        'missing_in_oris' => 'Missing in ORIS',
        'missing_in_dalin' => 'Missing in Dalin',
        'not_linked' => 'Not found in ORIS',
        'unknown' => 'Unknown',
    ],

    'errors' => [
        'too_many_chips' => 'ORIS can store at most :max chips.',
        'person_incomplete' => 'ORIS is missing required person data (:fields), so the chips cannot be written safely. Fill it in in ORIS.',
        'edit_failed' => 'ORIS rejected the chip change (status: :status).',
        'not_linked' => 'The registration was not found in ORIS.',
        'nothing_to_push' => 'The registration has no chip in Dalin, nothing is sent.',
        'pull_failed' => 'Could not take the chips over.',
        'no_club_key' => 'The ORIS club key (ORIS_GENERAL_CLUBKEY) is not configured.',
        'request_failed' => 'Could not connect to ORIS.',
        'invalid_response' => 'ORIS returned an unexpected response. Check the club key.',
    ],

    'overview' => [
        'navigation_label' => 'Chips Dalin × ORIS',
        'title' => 'SI chips comparison Dalin × ORIS',
        'description' => 'Overview of registrations and their SI chips in Dalin and in ORIS. You can take the chips over from ORIS into Dalin for each registration separately.',
        'load_failed' => 'Could not load ORIS',
        'columns' => [
            'member' => 'Registration',
            'dalin' => 'Chips in Dalin',
            'oris' => 'Chips in ORIS',
            'status' => 'Status',
            'none' => '—',
        ],
        'filters' => [
            'only_differences' => 'Differences only',
            'status' => 'Status',
            'active' => 'Active registrations only',
        ],
        'actions' => [
            'refresh' => [
                'label' => 'Refresh from ORIS',
                'notification_title' => 'ORIS data refreshed',
            ],
            'pull' => [
                'label' => 'Take over from ORIS',
                'modal_heading' => 'Take over chips from ORIS',
                'modal_description' => 'The chips of :name in Dalin will be replaced with the ORIS ones: :oris. The current Dalin chips (:dalin) will be overwritten.',
                'modal_submit' => 'Take over',
                'notification_title' => 'Chips taken over from ORIS',
                'notification_body' => ':name now has the same chips as ORIS.',
            ],
            'push' => [
                'label' => 'Send to ORIS',
                'modal_heading' => 'Send chips to ORIS',
                'modal_description' => 'In ORIS, :name will be set to: :dalin. The current ORIS chips (:oris) will be overwritten. Other personal data in ORIS stays unchanged.',
                'modal_submit' => 'Send to ORIS',
                'notification_title' => 'Chips sent to ORIS',
                'notification_body' => ':name now has the same chips in ORIS as in Dalin.',
                'notification_failed' => 'Could not send the chips to ORIS',
            ],
        ],
        'empty_heading' => 'No registrations',
    ],

    'profile' => [
        'sync_action' => 'Sync chip to ORIS',
        'differs_notice' => 'The chip in ORIS differs. In ORIS: :oris. In Dalin: :dalin.',
        'modal_heading' => 'Update the chip in ORIS too?',
        'modal_description' => 'In ORIS your chip is: :oris. In Dalin it is: :dalin. It is better to keep the chip up to date in both systems - entering directly at races and relay rosters use the chip from ORIS. Do you want to update it in ORIS too?',
        'modal_submit' => 'Yes, update in ORIS',
        'modal_cancel' => 'No, keep it',
        'notification_title' => 'The chip was updated in ORIS',
        'notification_failed' => 'Could not update the chip in ORIS',
    ],

];
