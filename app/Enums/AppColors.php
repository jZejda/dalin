<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Override;

enum AppColors: string implements HasColor
{
    case Primary = 'primary';
    case Success = 'success';
    case Warning = 'warning';
    case Danger = 'danger';
    case Info = 'info';
    case Gray = 'gray';

    public static function enumArray(): array
    {
        $trKey = 'app.colors.';

        return [
            self::Primary->value => __($trKey . self::Primary->value),
            self::Success->value => __($trKey . self::Success->value),
            self::Warning->value => __($trKey . self::Warning->value),
            self::Danger->value => __($trKey . self::Danger->value),
            self::Info->value => __($trKey . self::Info->value),
            self::Gray->value => __($trKey . self::Gray->value),
        ];
    }

    #[Override]
    public function getColor(): string|array|null
    {
        return $this->value;
    }
}
