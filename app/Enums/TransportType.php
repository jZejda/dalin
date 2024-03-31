<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TransportType: string implements HasLabel
{
    case Car = 'car';
    case Microbus = 'microbus';
    case Bus = 'bus';

    public function getLabel(): ?string
    {
        $trKey = 'transport-offer.type_enum_type.';
        return __($trKey . $this->value);
    }
}
