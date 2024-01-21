<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum PostStatus: int implements HasLabel, HasColor
{
    case Public = 0;
    case Private = 1;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Public => 'Veřejná',
            self::Private => 'Interní',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Public => 'success',
            self::Private => 'danger',
        };
    }
}
