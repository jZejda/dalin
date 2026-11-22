<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

/**
 * Physical SI chip type. Values match ORIS (`sitype*`).
 */
enum SiChipType: int implements HasLabel
{
    case All = 0;
    case Contact = 1;
    case Contactless = 2;
    case ContactlessBs11 = 3;
    case Manual = 4;
    case ToePunch = 5;

    public function getLabel(): string
    {
        return __('si-chip.type.'.$this->value);
    }
}
