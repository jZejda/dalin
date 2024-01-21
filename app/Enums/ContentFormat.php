<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum ContentFormat: int implements HasLabel, HasColor
{
    case Html = 1;
    case Markdown = 2;

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Html => 'HTML',
            self::Markdown => 'Markdown',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Html => 'info',
            self::Markdown => 'success',
        };
    }
}
