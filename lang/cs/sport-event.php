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

    'event_news' => [
        'content' => 'Obsah novinky',
        'date' => 'Datum novniky',
    ],


    'type_enum_credit_status' => [
        UserCreditStatus::Done->value => 'hotovo',
        UserCreditStatus::UnAssign->value => 'nepřiřazeno',
        UserCreditStatus::Open->value => 'otevřeno',
    ],

    'type_enum_credit_type' => [
        UserCreditType::CacheOut->value => 'Výdaj',
        UserCreditType::UserDonation->value => 'Mimořádný členský vklad',
        UserCreditType::MembershipFees->value => 'Členské příspěvky',
        UserCreditType::TransferCreditBetweenUsers->value => 'Přesun mezi uživateli',
        UserCreditType::InitialDeposit->value => 'Počáteční vklad',
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
        SportEventLinkType::Invitation->value => 'Rozpis',
        SportEventLinkType::Information->value => 'Pokyny',
        SportEventLinkType::StartList->value => 'Startovka',
        SportEventLinkType::Results->value => 'Výsledky',
        SportEventLinkType::CompetitionCentreMap->value => 'Plánek shromaždiště',
        SportEventLinkType::SplitTimes->value => 'Mezičasy',
        SportEventLinkType::Photos->value => 'Fotky',
        SportEventLinkType::Video->value => 'Video',
        SportEventLinkType::MapSamples->value => 'Ukázky mapy',
        SportEventLinkType::OldMap->value => 'Stará mapa',
        SportEventLinkType::RouteChoices->value => 'OB Postupy',
        SportEventLinkType::ClubStartList->value => 'Startovka po klubech',
        SportEventLinkType::EventWebsite->value => 'Web závodu',
        SportEventLinkType::ResultsForRanking->value => 'Výsledky pro ranking',
        SportEventLinkType::ResultsForCups->value => 'Výsledky pro žebříček',
        SportEventLinkType::ResultsOfClass->value => 'Výsledky kategorie',
        SportEventLinkType::ResultsForCupsAB->value => 'Výsledky pro žebříček A,B',
        SportEventLinkType::ResultsForMastersRanking->value => 'Výsledky pro ranking veteránů',

        SportEventLinkType::Accommodation->value => 'Ubytování',
        SportEventLinkType::Livelox->value => 'Livevox',
        SportEventLinkType::Other->value => 'Ostatní',
    ],

    'type_enum_entry_status' => [
        EntryStatus::Create->value => 'vytvořeno',
        EntryStatus::Edit->value => 'upaveno',
        EntryStatus::Cancel->value => 'stornováno',
    ],
];
