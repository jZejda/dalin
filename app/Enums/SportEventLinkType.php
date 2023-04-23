<?php

declare(strict_types=1);

namespace App\Enums;

enum SportEventLinkType: string
{
    case StartList = 'startList';
    case ResultList = 'resultList';
    case EventWebsite = 'eventWebsite';
    case TerrainPhotos = 'terrainPhotos';
    case Invitation = 'invitation';
    case Schedule = 'schedule';
    case Guidelines = 'guidelines';
    case Accommodation = 'accommodation';
    case MapSample = 'mapSample';
    case RouteChoices = 'routeChoices';
    case Livelox = 'livelox';
    case Oresults = 'oresults';
    case Photos = 'photos';
    case Other = 'other';

    public static function enumArray(): array
    {
        return [
            'startList' => __('sport-event.type_enum_links.' . self::StartList->value),
            'resultList' => __('sport-event.type_enum_links.' . self::ResultList->value),
            'eventWebsite' => __('sport-event.type_enum_links.' . self::EventWebsite->value),
            'terrainPhotos' => __('sport-event.type_enum_links.' . self::TerrainPhotos->value),
            'invitation' => __('sport-event.type_enum_links.' . self::Invitation->value),
            'schedule' => __('sport-event.type_enum_links.' . self::Schedule->value),
            'guidelines' => __('sport-event.type_enum_links.' . self::Guidelines->value),
            'accommodation' => __('sport-event.type_enum_links.' . self::Accommodation->value),
            'mapSample' => __('sport-event.type_enum_links.' . self::MapSample->value),
            'routeChoices' => __('sport-event.type_enum_links.' . self::RouteChoices->value),
            'livelox' => __('sport-event.type_enum_links.' . self::Livelox->value),
            'oresults' => __('sport-event.type_enum_links.' . self::Oresults->value),
            'photos' => __('sport-event.type_enum_links.' . self::Photos->value),
            'other' => __('sport-event.type_enum_links.' . self::Other->value),
        ];
    }

    public static function mapOrisIdtoEnum(?int $orisSourceTypeId): string
    {
        return match($orisSourceTypeId) {
            1 => self::Schedule->value, // ma byt rozpis?
            2 => self::Guidelines->value, // mají být pokyny neco jineho?
            4 => self::ResultList->value,
            11 => self::RouteChoices->value,
            13 => self::EventWebsite->value,
            0 => self::Other->value,
            default => self::Other->value,
        };
    }
}
