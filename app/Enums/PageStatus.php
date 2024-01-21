<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum PageStatus: string implements HasLabel, HasColor
{
    case Open = 'open';
    case Closed = 'close';
    case Draft = 'draft';
    case Archive = 'archive';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Open => 'Zveřejněno',
            self::Closed => 'Uzavřeno',
            self::Draft => 'Koncept',
            self::Archive => 'Archivováno',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Open => 'success',
            self::Closed => 'danger',
            self::Draft => 'info',
            self::Archive => 'warning',
        };
    }
}
