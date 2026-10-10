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

        'request_created_approve_button' => 'Schválit žádost',
        'request_created_reject_button' => 'Zamítnout žádost',

        'club' => [
            'seats_value' => '{1} :count místo|[2,4] :count místa|[5,*] :count míst',
            'driver_label' => 'Řidič',
            'passenger_label' => 'Spolujezdec',
            'direction_label' => 'Směr',
            'seats_label' => 'Počet míst',
            'reserved_seats_label' => 'Rezervovaná místa',
            'cancelled_seats_label' => 'Zrušená místa',
            'departure_label' => 'Místo odjezdu',
            'departure_from_label' => 'Odjezd z',
            'vehicle_label' => 'Tvoje auto',
        ],

        'request_created_club' => [
            'eyebrow' => 'Spolujízda · nová žádost',
            'title' => ':passenger chce jet s tebou.',
            'lead' => '{1} Žádá o :count místo na :event, :date.|[2,4] Žádá o :count místa na :event, :date.|[5,*] Žádá o :count míst na :event, :date.',
            'note_label' => 'Poznámka k žádosti',
            'fine' => 'Žádost můžeš vyřídit do dne závodu. Najdeš ji také v DaLinu v sekci Doprava.',
        ],

        'request_approved_club' => [
            'eyebrow' => 'Spolujízda · žádost schválena',
            'title' => 'Máš potvrzené místo v autě.',
            'lead' => 'Tvoje žádost o spolujízdu na :event, :date, byla schválena.',
            'status' => '{1} Schváleno · :count místo|[2,4] Schváleno · :count místa|[5,*] Schváleno · :count míst',
            'action' => 'Otevřít dopravu k závodu',
            'fine' => 'Domluv si s řidičem podrobnosti odjezdu. Rezervaci najdeš v DaLinu v sekci Doprava.',
        ],

        'request_rejected_club' => [
            'eyebrow' => 'Spolujízda · žádost zamítnuta',
            'title' => 'Tentokrát spolujízda nevyšla.',
            'lead' => 'Tvoje žádost o spolujízdu na :event, :date, byla zamítnuta.',
            'status' => 'Žádost zamítnuta',
            'action' => 'Prohlédnout další nabídky dopravy',
            'fine' => 'Podívej se na další možnosti dopravy k závodu nebo si zajisti jiný způsob cesty.',
        ],

        'request_cancelled_club' => [
            'eyebrow' => 'Spolujízda · pro řidiče',
            'title' => ':passenger ruší rezervaci.',
            'lead' => '{1} Na :event, :date, se ve tvém autě uvolnilo :count rezervované místo.|[2,4] Na :event, :date, se ve tvém autě uvolnila :count rezervovaná místa.|[5,*] Na :event, :date, se ve tvém autě uvolnilo :count rezervovaných míst.',
            'action' => 'Otevřít svoji nabídku dopravy',
            'fine' => 'Aktuální obsazení auta najdeš v sekci Doprava u závodu.',
        ],

        'offer_cancelled_club' => [
            'eyebrow' => 'Spolujízda · nabídka zrušena',
            'title' => 'Řidič zrušil nabídku dopravy.',
            'lead' => 'Spolujízda na :event, :date, se ruší.',
            'note_label' => 'Počítej s jinou dopravou',
            'note' => 'Tvoje žádost o místo v této nabídce už neplatí. Podívej se na další možnosti cesty.',
            'action' => 'Prohlédnout dopravu k závodu',
            'fine' => 'Aktuální nabídky najdeš v DaLinu v sekci Doprava.',
        ],
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
