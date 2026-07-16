<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PostStatus: int implements HasColor, HasLabel
{
    case Public = 0;
    case Private = 1;

    public function getLabel(): ?string
    {
        $trKey = 'content.enums.post_status.';

        return match ($this) {
            self::Public => __($trKey.self::Public->value),
            self::Private => __($trKey.self::Private->value),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Public => 'success',
            self::Private => 'danger',
        };
    }
}
