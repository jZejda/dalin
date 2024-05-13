<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AppRoles: string implements HasLabel, HasColor
{
    case SuperAdmin = 'super_admin';
    case ClubAdmin = 'club_admin';
    case EventMaster = 'event_master';
    case Member = 'member';
    case Racer = 'racer';
    case Redactor = 'redactor';
    case EventOrganizer = 'event_organizer';
    case BillingSpecialist = 'billing_specialist';

    public function getLabel(): ?string
    {
        $trKey = 'app-role.app_role_enum.';
        return __($trKey . $this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::SuperAdmin => 'danger',
            self::EventMaster => 'yellow',
            default => 'info',
        };
    }
}
