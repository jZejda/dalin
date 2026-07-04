<?php

declare(strict_types=1);

namespace App\Enums;

enum ServiceOrderStatus: string
{
    case Ordered = 'ordered';
    case Cancelled = 'cancelled';
    case Billed = 'billed';

    /** @return array<string, string> */
    public static function enumArray(): array
    {
        return [
            self::Ordered->value => __('sport-event.service_order_status_enum.'.self::Ordered->value),
            self::Cancelled->value => __('sport-event.service_order_status_enum.'.self::Cancelled->value),
            self::Billed->value => __('sport-event.service_order_status_enum.'.self::Billed->value),
        ];
    }

    public function label(): string
    {
        return __('sport-event.service_order_status_enum.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Ordered => 'warning',
            self::Cancelled => 'gray',
            self::Billed => 'success',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Ordered => 'heroicon-o-shopping-bag',
            self::Cancelled => 'heroicon-o-minus-circle',
            self::Billed => 'heroicon-o-banknotes',
        };
    }
}
