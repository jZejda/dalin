<?php

return [

    'sport' => [
        0 => 'Vše',
        1 => 'OB',
        2 => 'LOB',
        3 => 'MTBO',
        4 => 'TRAIL',
    ],

    'type' => [
        0 => 'Vše',
        1 => 'SI kontaktní',
        2 => 'SI bezkontaktní',
        3 => 'SI bezkon. BS11',
        4 => 'manuální',
        5 => 'ToePunch',
    ],

    'form' => [
        'si' => 'Číslo čipu',
        'sport' => 'Sport',
        'type' => 'Typ čipu',
        'extra_chips' => 'Další čipy',
        'extra_chips_helper' => 'Až dva další čipy, například pro jiný sport. Na přihlášku se předvyplní čip podle sportu závodu, jinak výchozí.',
        'add_chip' => 'Přidat čip',
    ],

    'table' => [
        'extra_chips' => 'Další čipy',
    ],

    'status' => [
        'in_sync' => 'Shodné',
        'differs' => 'Liší se',
        'missing_in_oris' => 'Chybí v ORISu',
        'missing_in_dalin' => 'Chybí v Dalinu',
        'not_linked' => 'Nenalezen v ORISu',
        'unknown' => 'Nelze zjistit',
    ],

    'errors' => [
        'pull_failed' => 'Čipy se nepodařilo převzít.',
        'no_club_key' => 'Není nastaven klubový klíč ORIS (ORIS_GENERAL_CLUBKEY).',
        'request_failed' => 'Nepodařilo se spojit s ORISem.',
        'invalid_response' => 'ORIS vrátil neočekávanou odpověď. Zkontroluj klubový klíč.',
    ],

    'overview' => [
        'navigation_label' => 'Čipy Dalin × ORIS',
        'title' => 'Porovnání SI čipů Dalin × ORIS',
        'description' => 'Přehled registrací a jejich SI čipů v Dalinu a v ORISu. Čip z ORISu si můžeš převzít do Dalinu u každé registrace zvlášť.',
        'load_failed' => 'ORIS se nepodařilo načíst',
        'columns' => [
            'member' => 'Registrace',
            'dalin' => 'Čipy v Dalinu',
            'oris' => 'Čipy v ORISu',
            'status' => 'Stav',
            'none' => '—',
        ],
        'filters' => [
            'only_differences' => 'Jen rozdíly',
            'status' => 'Stav',
            'active' => 'Jen aktivní registrace',
        ],
        'actions' => [
            'refresh' => [
                'label' => 'Obnovit z ORISu',
                'notification_title' => 'Data z ORISu byla obnovena',
            ],
            'pull' => [
                'label' => 'Převzít z ORISu',
                'modal_heading' => 'Převzít čipy z ORISu',
                'modal_description' => 'Čipy registrace :name v Dalinu budou nahrazeny čipy z ORISu: :oris. Dosavadní čipy v Dalinu (:dalin) se přepíšou.',
                'modal_submit' => 'Převzít',
                'notification_title' => 'Čipy převzaty z ORISu',
                'notification_body' => 'Registrace :name má nyní čipy shodné s ORISem.',
            ],
        ],
        'empty_heading' => 'Žádné registrace',
    ],

];
