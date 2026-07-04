<?php

declare(strict_types=1);

namespace App\Enums;

enum VehicleType: string
{
    case PassengerCar = 'passengerCar';
    case Van = 'van';
    case Bus = 'bus';
    case Plane = 'plane';

    /** @return array<string, string> */
    public static function enumArray(): array
    {
        return [
            self::PassengerCar->value => __('vehicle.type_enum.'.self::PassengerCar->value),
            self::Van->value => __('vehicle.type_enum.'.self::Van->value),
            self::Bus->value => __('vehicle.type_enum.'.self::Bus->value),
            self::Plane->value => __('vehicle.type_enum.'.self::Plane->value),
        ];
    }

    public function label(): string
    {
        return __('vehicle.type_enum.'.$this->value);
    }

    public function icon(): string
    {
        return match ($this) {
            self::PassengerCar => 'heroicon-o-truck',
            self::Van => 'heroicon-o-truck',
            self::Bus => 'heroicon-o-truck',
            self::Plane => 'heroicon-o-paper-airplane',
        };
    }
}
