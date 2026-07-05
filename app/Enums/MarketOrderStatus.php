<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Override;

enum MarketOrderStatus: string implements HasColor, HasIcon, HasLabel
{
    case Ordered = 'ordered';
    case Cancelled = 'cancelled';
    case Billed = 'billed';

    /** @return array<string, string> */
    public static function enumArray(): array
    {
        return [
            self::Ordered->value => __('marketplace.order_status_enum.'.self::Ordered->value),
            self::Cancelled->value => __('marketplace.order_status_enum.'.self::Cancelled->value),
            self::Billed->value => __('marketplace.order_status_enum.'.self::Billed->value),
        ];
    }

    #[Override]
    public function getLabel(): string
    {
        return __('marketplace.order_status_enum.'.$this->value);
    }

    #[Override]
    public function getColor(): string
    {
        return match ($this) {
            self::Ordered => 'warning',
            self::Cancelled => 'gray',
            self::Billed => 'success',
        };
    }

    #[Override]
    public function getIcon(): string
    {
        return match ($this) {
            self::Ordered => 'heroicon-o-shopping-cart',
            self::Cancelled => 'heroicon-o-minus-circle',
            self::Billed => 'heroicon-o-banknotes',
        };
    }
}
