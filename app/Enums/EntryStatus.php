<?php

declare(strict_types=1);

namespace App\Enums;

enum EntryStatus: string
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
}
