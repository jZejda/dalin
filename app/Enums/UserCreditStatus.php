<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;

enum UserCreditStatus: string implements HasColor
{
    case Done = 'done';
    case UnAssign = 'unAssign';
    case Open = 'open';

    public static function enumArray(): array
    {
        return [
            'done'      => __('sport-event.type_enum_credit_status.' . self::Done->value),
            'unAssign'  => __('sport-event.type_enum_credit_status.' . self::UnAssign->value),
            'open'      => __('sport-event.type_enum_credit_status.' . self::Open->value),
        ];
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::Done => 'success',
            self::UnAssign => 'danger',
            self::Open => 'warning',
        };
    }
}
