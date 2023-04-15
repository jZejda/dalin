<?php

declare(strict_types=1);

namespace App\Enums;

enum UserCreditStatus: string
{
    case Done = 'done';
    case UnAssign = 'unAssign';
    case Open = 'open';

    public static function enumArray(): array
    {
        return [
            'done'      => __('sport-event.type_enum.' . self::Done->value),
            'unAssign'  => __('sport-event.type_enum.' . self::UnAssign->value),
            'open'      => __('sport-event.type_enum.' . self::Open->value),
        ];
    }
}
