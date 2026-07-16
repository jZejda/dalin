<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;
use Filament\Support\Contracts\HasColor;

enum ContentFormat: int implements HasLabel, HasColor
{
    case Html = 1;
    case Markdown = 2;
    case TipTapJson = 3;

    public function getLabel(): ?string
    {
        $trKey = 'content.enums.content_format.';

        return match ($this) {
            self::Html => __($trKey.self::Html->value),
            self::Markdown => __($trKey.self::Markdown->value),
            self::TipTapJson => __($trKey.self::TipTapJson->value),
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Html => 'info',
            self::Markdown => 'success',
            self::TipTapJson => 'warning',
        };
    }
}
