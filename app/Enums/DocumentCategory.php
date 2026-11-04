<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

/**
 * Category of a club document; the case order is the order of sections on the public /dokumenty page.
 */
enum DocumentCategory: string implements HasLabel, HasColor
{
    case AnnualReport = 'annual_report';
    case Statutes = 'statutes';
    case Minutes = 'minutes';
    case Other = 'other';

    public function getLabel(): string
    {
        return __('document.enums.category.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::AnnualReport => 'success',
            self::Statutes => 'info',
            self::Minutes => 'warning',
            self::Other => 'gray',
        };
    }
}
