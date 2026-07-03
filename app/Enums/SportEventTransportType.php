<?php

declare(strict_types=1);

namespace App\Enums;

enum SportEventTransportType: string
{
    case ClubOnly = 'clubOnly';
    case Combined = 'combined';
    case SelfOnly = 'selfOnly';
    case None = 'none';

    /** @return array<string, string> */
    public static function enumArray(): array
    {
        return [
            self::ClubOnly->value => __('sport-event.transport_type_enum.'.self::ClubOnly->value),
            self::Combined->value => __('sport-event.transport_type_enum.'.self::Combined->value),
            self::SelfOnly->value => __('sport-event.transport_type_enum.'.self::SelfOnly->value),
            self::None->value => __('sport-event.transport_type_enum.'.self::None->value),
        ];
    }

    public function allowsClubVehicles(): bool
    {
        return $this === self::ClubOnly || $this === self::Combined;
    }
}
