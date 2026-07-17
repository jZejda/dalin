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
    'request_cancelled_done' => 'Žádost byla zrušena.',

    'mail' => [
        'request_created_subject' => 'Nová žádost o spolujízdu',
        'request_approved_subject' => 'Žádost o spolujízdu schválena',
        'request_rejected_subject' => 'Žádost o spolujízdu zamítnuta',
        'request_cancelled_subject' => 'Spolujezdec zrušil rezervaci',
        'offer_cancelled_subject' => 'Nabídka dopravy byla zrušena',

        'offer_cancelled_heading' => 'Nabídka dopravy byla zrušena',
        'offer_cancelled_intro' => 'Řidič **:driver** zrušil nabídku dopravy na závod **:event**, ve které jsi měl žádost o místo.',
        'try_another_offer_footer' => 'Zkus jinou nabídku dopravy na stránce Doprava u závodu.',

        'request_cancelled_heading' => 'Spolujezdec zrušil rezervaci',
        'request_cancelled_intro' => '**:passenger** zrušil svou rezervaci ve tvé nabídce dopravy na závod **:event**.',
        'request_cancelled_seats_label' => 'Počet uvolněných míst',
        'request_cancelled_footer' => 'Místa jsou opět volná pro další zájemce.',

        'request_created_heading' => 'Nová žádost o spolujízdu',
        'request_created_intro' => '**:passenger** má zájem o místo ve tvé nabídce dopravy na závod **:event**.',
        'request_created_cta' => 'Žádost můžeš vyřídit rovnou z tohoto e-mailu:',
        'request_created_approve_button' => 'Schválit žádost',
        'request_created_reject_button' => 'Zamítnout žádost',
        'request_created_footer' => 'Odkazy platí do dne konání závodu. Žádosti najdeš i v aplikaci na stránce Doprava u závodu.',

        'request_decided_heading_approved' => 'Tvoje žádost o spolujízdu byla schválena 🎉',
        'request_decided_heading_rejected' => 'Tvoje žádost o spolujízdu byla zamítnuta',
        'request_decided_race_label' => 'Závod',
        'request_decided_driver_label' => 'Řidič',
        'request_decided_footer_approved' => 'Místo v autě je pro tebe rezervované. Detaily domluv přímo s řidičem.',
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
