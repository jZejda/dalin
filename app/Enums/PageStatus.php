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
        $trKey = 'content.enums.page_status.';

        return match ($this) {
            self::Open => __($trKey.self::Open->value),
            self::Closed => __($trKey.self::Closed->value),
            self::Draft => __($trKey.self::Draft->value),
            self::Archive => __($trKey.self::Archive->value),
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
