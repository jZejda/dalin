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
