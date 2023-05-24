<?php

declare(strict_types=1);

namespace App\Enums;

enum AppRoles: string
{
    case SuperAdmin = 'super_admin';
    case EventMaster = 'event_master';
    case Member = 'member';
    case Racer = 'racer';
    case Redactor = 'redactor';
    case BillingSpecialist = 'billing_specialist';

    public static function enumArray(): array
    {
        $trKey = 'app-role.app_role_enum.';
        return [
            'super_admin' => __($trKey . self::SuperAdmin->value),
            'event_master' => __($trKey . self::EventMaster->value),
            'member' => __($trKey . self::Member->value),
            'redactor' => __($trKey . self::Redactor->value),
            'billing_specialist' => __($trKey . self::BillingSpecialist->value),
        ];
    }
}
