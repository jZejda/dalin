<?php

declare(strict_types=1);

namespace App\Enums;

enum UserCreditSource: string
{
    case User = 'user';
    case System = 'system';
    case Cron = 'cron';

    public static function enumArray(): array
    {
        $trKey = 'user-credit.credit_source_enum.';

        return [
            'user' => __($trKey.self::User->value),
            'system' => __($trKey.self::System->value),
            'cron' => __($trKey.self::Cron->value),
        ];
    }
}
