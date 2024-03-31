<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TransportOfferDirection: string implements HasLabel, HasColor, HasIcon
{
    case OnlyThere = 'onlyThere';
    case OnlyBack = 'onlyBack';
    case BothDirection = 'bothDirection';

    public function getLabel(): ?string
    {
        $trKey = 'transport-offer.type_enum_directions.';
        return __($trKey . $this->value);
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::OnlyBack => 'heroicon-m-arrow-left',
            self::OnlyThere => 'heroicon-m-arrow-right',
            self::BothDirection => 'heroicon-m-arrows-right-left',
        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::OnlyBack, self::OnlyThere => 'warning',
            self::BothDirection => 'success',
        };
    }
}
