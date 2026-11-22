<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Result of comparing the SI chips of a race profile in Dalin and in ORIS.
 */
enum SiSyncStatus: string implements HasColor, HasLabel
{
    case InSync = 'in_sync';
    case Differs = 'differs';
    case MissingInOris = 'missing_in_oris';
    case MissingInDalin = 'missing_in_dalin';
    case NotLinked = 'not_linked';
    case Unknown = 'unknown';

    public function getLabel(): string
    {
        return __('si-chip.status.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::InSync => 'success',
            self::Differs => 'warning',
            self::MissingInOris, self::MissingInDalin => 'info',
            self::NotLinked, self::Unknown => 'gray',
        };
    }

    public function isDifference(): bool
    {
        return in_array($this, [self::Differs, self::MissingInOris, self::MissingInDalin], true);
    }
}
