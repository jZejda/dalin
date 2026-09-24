<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\SportDiscipline;
use Filament\Support\Contracts\HasLabel;

/**
 * Relay team type — values match the ORIS discipline short names (RE / SR / TE).
 */
enum RelayType: string implements HasLabel
{
    case Relay = 'RE';
    case SprintRelay = 'SR';
    case Team = 'TE';

    public static function fromDiscipline(?SportDiscipline $discipline): self
    {
        return self::tryFrom((string) $discipline?->short_name) ?? self::Relay;
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Relay => __('sport-event.relation_relay_teams.relay_type_relay'),
            self::SprintRelay => __('sport-event.relation_relay_teams.relay_type_sprint_relay'),
            self::Team => __('sport-event.relation_relay_teams.relay_type_team'),
        };
    }
}
