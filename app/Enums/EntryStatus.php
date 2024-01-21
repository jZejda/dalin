<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EntryStatus: string implements HasLabel, HasColor
{
    case Create = 'create';
    case Edit = 'edit';
    case Cancel = 'cancel';

    public static function enumArray(): array
    {
        $trKey = 'sport-event.type_enum_entry_status.';
        return [
            'cancel' => __($trKey . self::Cancel->value),
            'create' => __($trKey . self::Create->value),
            'edit' => __($trKey . self::Edit->value),
        ];
    }

    public function getLabel(): ?string
    {
        $trKey = 'sport-event.type_enum_entry_status.';
        return __($trKey . $this->value);
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Edit => 'warning',
            self::Create => 'success',
            self::Cancel => 'danger',
        };
    }
}
