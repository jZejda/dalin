<?php

declare(strict_types=1);

namespace App\Enums;

use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

/**
 * Club's entry-fee payment state of an event, derived from the ORIS getEventBalance settlement.
 */
enum OrisPaymentState: string implements HasColor, HasIcon, HasLabel
{
    case Unpaid = 'unpaid';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Overpaid = 'overpaid';

    public static function fromAmounts(float $paid, float $toBePaid): self
    {
        return match (true) {
            $toBePaid < 0 => self::Overpaid,
            $toBePaid == 0 => self::Paid,
            $paid > 0 => self::PartiallyPaid,
            default => self::Unpaid,
        };
    }

    public function getLabel(): string
    {
        return __('sport-event.oris_status.payment_states.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Unpaid => 'danger',
            self::PartiallyPaid => 'warning',
            self::Paid => 'success',
            self::Overpaid => 'info',
        };
    }

    public function getIcon(): LucideIcon
    {
        return match ($this) {
            self::Unpaid => LucideIcon::CircleAlert,
            self::PartiallyPaid => LucideIcon::CircleDashed,
            self::Paid => LucideIcon::CircleCheck,
            self::Overpaid => LucideIcon::CirclePlus,
        };
    }

    /**
     * Whether the club still owes the organizer something.
     */
    public function isOutstanding(): bool
    {
        return $this === self::Unpaid || $this === self::PartiallyPaid;
    }
}
