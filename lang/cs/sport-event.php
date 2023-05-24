<?php

use App\Enums\EntryStatus;
use App\Enums\SportEventType;
use App\Enums\SportEventMarkerType;
use App\Enums\SportEventLinkType;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;

return [

    /*
    |--------------------------------------------------------------------------
    | SportEvent Resource
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default strings in SportEvent
    | resource.
    |
    */

    'event_type' => 'Typ akcí',


    'type_enum_credit_status' => [
        UserCreditStatus::Done->value => 'hotovo',
        UserCreditStatus::UnAssign->value => 'nepřiřazeno',
        UserCreditStatus::Open->value => 'otevřeno',
    ],

    'type_enum_credit_type' => [
        UserCreditType::CacheOut->value => 'Výdaj',
        UserCreditType::UserDonation->value => 'Mimořádný členský vklad',
    ],

    'type_enum' => [
        SportEventType::Race->value => 'Závod',
        SportEventType::Training->value => 'Trénink',
        SportEventType::TrainingCamp->value => 'Soustředění',
        SportEventType::Other->value => 'Ostatní',
    ],

    'type_enum_markers' => [
        SportEventMarkerType::StageStart->value => 'Start etapy',
        SportEventMarkerType::Parking->value => 'Parkování',
        SportEventMarkerType::Other->value => 'Ostatní',
    ],

    'type_enum_links' => [
        SportEventLinkType::StartList->value => 'Startovka',
        SportEventLinkType::ResultList->value => 'Výsledky',
        SportEventLinkType::EventWebsite->value => 'Stránky závodu',
        SportEventLinkType::TerrainPhotos->value => 'Fotky terénu',
        SportEventLinkType::Invitation->value => 'Pozvánka',
        SportEventLinkType::Schedule->value => 'Rozpis',
        SportEventLinkType::Guidelines->value => 'Pokyny',
        SportEventLinkType::Accommodation->value => 'Ubytování',
        SportEventLinkType::MapSample->value => 'Ukázky mapy',
        SportEventLinkType::RouteChoices->value => 'Postupy',
        SportEventLinkType::Livelox->value => 'Livevox',
        SportEventLinkType::Oresults->value => 'Rresult',
        SportEventLinkType::Photos->value => 'Fotky',
        SportEventLinkType::Other->value => 'Ostatní',
    ],

    'type_enum_entry_status' => [
        EntryStatus::Create->value => 'vytvořeno',
        EntryStatus::Edit->value => 'upaveno',
        EntryStatus::Cancel->value => 'stornováno',
    ],
];
