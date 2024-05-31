<?php

declare(strict_types=1);

namespace App\Enums;

enum SportEventLinkType: string
{
    case Invitation = 'invitation';
    case Information = 'information';
    case StartList = 'startList';
    case Results = 'results';
    case CompetitionCentreMap = 'competitionCentreMap';
    case SplitTimes = 'splitTimes';
    case Photos = 'photos';
    case Video = 'video';
    case MapSamples = 'mapSamples';
    case OldMap = 'oldMap';
    case RouteChoices = 'routeChoices';
    case ClubStartList = 'clubStartList';
    case EventWebsite = 'eventWebsite';
    case ResultsForRanking = 'resultsForRanking';
    case ResultsForCups = 'resultsForCups';
    case ResultsOfClass = 'resultsOfClass';
    case ResultsForCupsAB = 'resultsForCupsAB';
    case ResultsForMastersRanking = 'resultsForMastersRanking';

    case Accommodation = 'accommodation';
    case Livelox = 'livelox';
    case Other = 'other';

    public static function enumArray(): array
    {
        $trKey = 'sport-event.type_enum_links.';

        return [
            'invitation' => __($trKey.self::Invitation->value),
            'information' => __($trKey.self::Information->value),
            'startList' => __($trKey.self::StartList->value),
            'results' => __($trKey.self::Results->value),
            'competitionCentreMap' => __($trKey.self::CompetitionCentreMap->value),
            'splitTimes' => __($trKey.self::SplitTimes->value),
            'photos' => __($trKey.self::Photos->value),
            'video' => __($trKey.self::Video->value),
            'mapSamples' => __($trKey.self::MapSamples->value),
            'oldMap' => __($trKey.self::OldMap->value),
            'routeChoices' => __($trKey.self::RouteChoices->value),
            'clubStartList' => __($trKey.self::ClubStartList->value),
            'eventWebsite' => __($trKey.self::EventWebsite->value),
            'resultsForRanking' => __($trKey.self::ResultsForRanking->value),
            'resultsForCups' => __($trKey.self::ResultsForCups->value),
            'resultsOfClass' => __($trKey.self::ResultsOfClass->value),
            'resultsForCupsAB' => __($trKey.self::ResultsForCupsAB->value),
            'resultsForMastersRanking' => __($trKey.self::ResultsForMastersRanking->value),

            'accommodation' => __($trKey.self::Accommodation->value),
            'livelox' => __($trKey.self::Livelox->value),
            'other' => __($trKey.self::Other->value),
        ];
    }

    public static function mapOrisIdtoEnum(?int $orisSourceTypeId): string
    {
        return match ($orisSourceTypeId) {
            1 => self::Invitation->value,
            2 => self::Information->value,
            3 => self::StartList->value,
            4 => self::Results->value,
            5 => self::CompetitionCentreMap->value,
            6 => self::SplitTimes->value,
            7 => self::Photos->value,
            8 => self::Video->value,
            9 => self::MapSamples->value,
            10 => self::OldMap->value,
            11 => self::RouteChoices->value,
            12 => self::ClubStartList->value,
            13 => self::EventWebsite->value,
            14 => self::ResultsForRanking->value,
            15 => self::ResultsForCups->value,
            16 => self::ResultsOfClass->value,
            17 => self::ResultsForCupsAB->value,
            18 => self::ResultsForMastersRanking->value,
            default => self::Other->value,
        };
    }
}
