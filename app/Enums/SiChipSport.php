<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Sport an SI chip is intended for. Values match ORIS (`sisport*`) and `sport_lists.id`.
 */
enum SiChipSport: int implements HasLabel
{
    case All = 0;
    case Ob = 1;
    case Lob = 2;
    case Mtbo = 3;
    case Trail = 4;

    public function getLabel(): string
    {
        return __('si-chip.sport.'.$this->value);
    }
}
