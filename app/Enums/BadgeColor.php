<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Colors\Color;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum BadgeColor: string implements HasColor, HasLabel
{
    case Red = 'red';
    case Orange = 'orange';
    case Amber = 'amber';
    case Yellow = 'yellow';
    case Lime = 'lime';
    case Green = 'green';
    case Emerald = 'emerald';
    case Teal = 'teal';
    case Cyan = 'cyan';
    case Sky = 'sky';
    case Blue = 'blue';
    case Indigo = 'indigo';
    case Violet = 'violet';
    case Purple = 'purple';
    case Fuchsia = 'fuchsia';
    case Pink = 'pink';
    case Rose = 'rose';

    public static function random(): self
    {
        $cases = self::cases();

        return $cases[array_rand($cases)];
    }

    /** @return array<string, string> */
    public static function enumArray(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }

        return $options;
    }

    public function getLabel(): string
    {
        return __('badge-color.badge_color_enum.'.$this->value);
    }

    /** @return array<int, string> */
    public function getColor(): array
    {
        return match ($this) {
            self::Red => Color::Red,
            self::Orange => Color::Orange,
            self::Amber => Color::Amber,
            self::Yellow => Color::Yellow,
            self::Lime => Color::Lime,
            self::Green => Color::Green,
            self::Emerald => Color::Emerald,
            self::Teal => Color::Teal,
            self::Cyan => Color::Cyan,
            self::Sky => Color::Sky,
            self::Blue => Color::Blue,
            self::Indigo => Color::Indigo,
            self::Violet => Color::Violet,
            self::Purple => Color::Purple,
            self::Fuchsia => Color::Fuchsia,
            self::Pink => Color::Pink,
            self::Rose => Color::Rose,
        };
    }

    /** CSS color value for the given shade, used as the badge background. */
    public function shade(int $shade = 500): string
    {
        return $this->getColor()[$shade];
    }
}
