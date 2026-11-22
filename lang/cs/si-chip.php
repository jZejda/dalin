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
        'too_many_chips' => 'ORIS umí uložit nejvýše :max čipy.',
        'person_incomplete' => 'V ORISu chybí povinné údaje osoby (:fields), čipy proto nelze bezpečně zapsat. Doplň je v ORISu.',
        'edit_failed' => 'ORIS odmítl změnu čipů (stav: :status).',
        'not_linked' => 'Registrace nebyla v ORISu nalezena.',
        'nothing_to_push' => 'Registrace nemá v Dalinu žádný čip, nic se neodešle.',
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
            'push' => [
                'label' => 'Odeslat do ORISu',
                'modal_heading' => 'Odeslat čipy do ORISu',
                'modal_description' => 'V ORISu bude u registrace :name nastaveno: :dalin. Dosavadní čipy v ORISu (:oris) se přepíšou. Ostatní údaje osoby v ORISu zůstanou beze změny.',
                'modal_submit' => 'Odeslat do ORISu',
                'notification_title' => 'Čipy odeslány do ORISu',
                'notification_body' => 'Registrace :name má v ORISu stejné čipy jako v Dalinu.',
                'notification_failed' => 'Čipy se nepodařilo odeslat do ORISu',
            ],
        ],
        'empty_heading' => 'Žádné registrace',
    ],

    'profile' => [
        'sync_action' => 'Synchronizovat čip do ORISu',
        'differs_notice' => 'Čip v ORISu se liší. V ORISu: :oris. V Dalinu: :dalin.',
        'modal_heading' => 'Aktualizovat čip také v ORISu?',
        'modal_description' => 'V ORISu máš čip: :oris. V Dalinu máš: :dalin. Je lepší mít čip aktuální v obou systémech - na přihlášení přímo na závodech a na soupisky štafet se bere čip z ORISu. Chceš ho aktualizovat i v ORISu?',
        'modal_submit' => 'Ano, aktualizovat v ORISu',
        'modal_cancel' => 'Ne, nechat',
        'notification_title' => 'Čip byl aktualizován v ORISu',
        'notification_failed' => 'Čip se nepodařilo aktualizovat v ORISu',
    ],

];
