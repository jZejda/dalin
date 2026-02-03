<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Override;

enum AppHeroIcons: string implements HasIcon
{
    case Trophy = 'trophy';
    case Track = 'truck';
    case Watch = 'swatch';
    case Tag = 'tag';
    case Ticket = 'ticket';
    case Trash = 'trash';
    case CalendarDays = 'calendar-days';
    case Clock = 'clock';
    case Flag = 'flag';
    case ExclamationCircle = 'exclamation-circle';

    public static function enumArray(): array
    {
        $trKey = 'app.icons.';

        return [
            self::Trophy->value => __($trKey . self::Trophy->value),
            self::Track->value => __($trKey . self::Track->value),
            self::Watch->value => __($trKey . self::Watch->value),
            self::Tag->value => __($trKey . self::Tag->value),
            self::Ticket->value => __($trKey . self::Ticket->value),
            self::Trash->value => __($trKey . self::Trash->value),
            self::CalendarDays->value => __($trKey . self::CalendarDays->value),
            self::Clock->value => __($trKey . self::Clock->value),
            self::Flag->value => __($trKey . self::Flag->value),
            self::ExclamationCircle->value => __($trKey . self::ExclamationCircle->value),
        ];
    }

    #[Override]
    public function getIcon(): string
    {
        return match ($this) {
            self::Trophy => 'heroicon-o-trophy',
            self::Track => 'heroicon-o-truck',
            self::Watch => 'heroicon-o-swatch',
            self::Tag => 'heroicon-o-tag',
            self::Ticket => 'heroicon-o-ticket',
            self::Trash => 'heroicon-o-trash',
            self::CalendarDays => 'heroicon-m-calendar-days',
            self::Clock => 'heroicon-m-clock',
            self::Flag => 'heroicon-m-flag',
            self::ExclamationCircle => 'heroicon-m-exclamation-circle',
        };
    }
}
