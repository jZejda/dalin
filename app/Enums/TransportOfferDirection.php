<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum TransportOfferDirection: string implements HasLabel
{
    case OnlyThere = 'onlyThere';
    case OnlyBack = 'onlyBack';
    case BothDirection = 'bothDirection';

    #[\Override] public function getLabel(): ?string
    {
        $trKey = 'transport-offer.type_enum_directions.';
        return __($trKey . $this->value);
    }
}
