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
        return [
            'super_admin' => self::SuperAdmin,
            'event_master' => self::EventMaster,
            'member' => self::Member,
            'racer' => self::Racer,
            'redactor' => self::Redactor,
            'billing_specialist' => self::BillingSpecialist,
        ];
    }
}
