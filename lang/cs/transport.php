<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Transport Module
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings for transport
    | offers and requests (spolujízda na závody).
    |
    */

    'page_title' => 'Doprava',
    'offers_heading' => 'Nabídky dopravy',
    'offer_transport' => 'Nabídnout dopravu',
    'edit_offer' => 'Upravit nabídku',

    'vehicle' => 'Vozidlo',
    'club_vehicle_prefix' => '[Klub]',
    'driver' => 'Nabízí',
    'departure_place' => 'Odkud',
    'direction' => 'Směr',
    'seats_offered' => 'Nabízených míst',
    'free_seats' => 'Volných míst',
    'distance_km' => 'Vzdálenost',
    'distance_km_suffix' => 'km',
    'contribution' => 'Příspěvek',
    'contribution_suffix' => 'Kč',
    'contribution_helper' => 'Nech prázdné, pokud příspěvek nechceš.',
    'without_contribution' => 'Bez příspěvku',
    'active' => 'Aktivní',

    'empty_offers' => 'Zatím žádné nabídky dopravy',
    'empty_offers_description' => 'Buď první, kdo nabídne spolujízdu na tento závod.',

    'request_seat' => 'Obsadit místo',
    'seats' => 'Počet míst',
    'user' => 'Uživatel',
    'trip_params' => 'Parametry cesty',
    'request_status' => 'Stav žádosti',
    'note' => 'Poznámka',
    'note_placeholder' => 'Např. kolik mám zavazadel, kde mě může vyzvednout…',
    'request_sent' => 'Žádost odeslána',
    'request_sent_body' => 'Řidič dostal e-mail a žádost může schválit nebo zamítnout.',
    'requests_for_my_offers' => 'Žádosti o místa v mých nabídkách',
    'my_requests' => 'Moje žádosti o spolujízdu',
    'approve' => 'Schválit',
    'reject' => 'Zamítnout',
    'cancel_request' => 'Zrušit žádost',
    'request_approved' => 'Žádost schválena, žadatel dostal e-mail.',
    'request_rejected_capacity' => 'Nedostatek volných míst — žádost byla zamítnuta.',
    'request_rejected_done' => 'Žádost zamítnuta, žadatel dostal e-mail.',
    'cancel_request_modal_heading' => 'Zrušit žádost o spolujízdu?',
    'cancel_request_modal_description' => 'Řidič bude informován, místo se uvolní pro ostatní.',
    'cancel_request_modal_submit' => 'Ano, zrušit žádost',
    'tab_free_seats' => 'Volná místa ve všech aktivních nabídkách',
    'request_cancelled_done' => 'Žádost byla zrušena.',

    'mail' => [
        'request_created_subject' => 'Nová žádost o spolujízdu',
        'request_approved_subject' => 'Žádost o spolujízdu schválena',
        'request_rejected_subject' => 'Žádost o spolujízdu zamítnuta',
        'request_cancelled_subject' => 'Spolujezdec zrušil rezervaci',
        'offer_cancelled_subject' => 'Nabídka dopravy byla zrušena',

        'request_cancelled_seats_label' => 'Počet uvolněných míst',

        'request_created_approve_button' => 'Schválit žádost',
        'request_created_reject_button' => 'Zamítnout žádost',

        'request_created_club' => [
            'eyebrow' => 'Spolujízda · nová žádost',
            'title' => ':passenger chce jet s tebou.',
            'lead' => '{1} Žádá o :count místo na závod :event, :date.|[2,4] Žádá o :count místa na závod :event, :date.|[5,*] Žádá o :count míst na závod :event, :date.',
            'seats_value' => '{1} :count místo|[2,4] :count místa|[5,*] :count míst',
            'vehicle_label' => 'Tvoje auto',
            'note_label' => 'Poznámka od :passenger',
            'fine' => 'Žádost můžeš vyřídit do dne závodu. Najdeš ji také v aplikaci na stránce Doprava u závodu.',
        ],

        'request_approved_club' => [
            'eyebrow' => 'Spolujízda · žádost schválena',
            'title' => 'Máš místo v autě.',
            'lead' => 'Žádost o spolujízdu na závod :event, :date je schválená. Detaily cesty domluv přímo s řidičem.',
            'action' => 'Zobrazit dopravu u závodu',
            'fine' => 'Když nakonec nepojedeš, zruš žádost v aplikaci na stránce Doprava u závodu, ať se místo uvolní.',
        ],

        'request_rejected_club' => [
            'eyebrow' => 'Spolujízda · žádost zamítnuta',
            'title' => 'Tentokrát to nevyšlo.',
            'lead' => 'Žádost o spolujízdu na závod :event, :date byla zamítnuta. Zkus jinou nabídku dopravy.',
            'action' => 'Najít jinou dopravu',
            'fine' => 'Všechny nabídky dopravy najdeš v aplikaci na stránce Doprava u závodu.',
        ],

        'request_cancelled_club' => [
            'eyebrow' => 'Spolujízda · zrušená rezervace',
            'title' => 'Místa v autě se uvolnila.',
            'lead' => '{1} :passenger s tebou na závod :event, :date nepojede — :count místo je opět volné.|[2,4] :passenger s tebou na závod :event, :date nepojede — :count místa jsou opět volná.|[5,*] :passenger s tebou na závod :event, :date nepojede — :count míst je opět volných.',
            'action' => 'Zobrazit moji nabídku',
            'fine' => 'Uvolněná místa se v nabídce zobrazují ostatním členům automaticky.',
        ],

        'offer_cancelled_club' => [
            'eyebrow' => 'Spolujízda · nabídka zrušena',
            'title' => 'Odvoz na závod se ruší.',
            'lead' => 'Řidič zrušil nabídku dopravy na závod :event, :date, takže tvoje žádost o místo padá. Zkus jinou nabídku dopravy.',
            'action' => 'Najít jinou dopravu',
            'fine' => 'Všechny nabídky dopravy najdeš v aplikaci na stránce Doprava u závodu.',
        ],

        'request_decided_driver_label' => 'Řidič',
    ],

    'direction_enum' => [
        'there' => 'Jen tam',
        'back' => 'Jen zpět',
        'both' => 'Tam i zpět',
    ],

    'request_status_enum' => [
        'pending' => 'Čeká na schválení',
        'approved' => 'Schváleno',
        'rejected' => 'Zamítnuto',
        'cancelled' => 'Zrušeno',
    ],
];
