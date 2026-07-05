<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Override;

enum MarketOfferStatus: string implements HasColor, HasIcon, HasLabel
{
    case Active = 'active';
    case Closed = 'closed';
    case Billed = 'billed';

    /** @return array<string, string> */
    public static function enumArray(): array
    {
        return [
            self::Active->value => __('marketplace.offer_status_enum.'.self::Active->value),
            self::Closed->value => __('marketplace.offer_status_enum.'.self::Closed->value),
            self::Billed->value => __('marketplace.offer_status_enum.'.self::Billed->value),
        ];
    }

    #[Override]
    public function getLabel(): string
    {
        return __('marketplace.offer_status_enum.'.$this->value);
    }

    #[Override]
    public function getColor(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Closed => 'warning',
            self::Billed => 'gray',
        };
    }

    #[Override]
    public function getIcon(): string
    {
        return match ($this) {
            self::Active => 'heroicon-o-megaphone',
            self::Closed => 'heroicon-o-lock-closed',
            self::Billed => 'heroicon-o-banknotes',
        };
    }
}
