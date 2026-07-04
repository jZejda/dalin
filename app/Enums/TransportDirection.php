<?php

declare(strict_types=1);

namespace App\Enums;

enum TransportDirection: string
{
    case There = 'there';
    case Back = 'back';
    case Both = 'both';

    /** @return array<string, string> */
    public static function enumArray(): array
    {
        return [
            self::There->value => __('transport.direction_enum.'.self::There->value),
            self::Back->value => __('transport.direction_enum.'.self::Back->value),
            self::Both->value => __('transport.direction_enum.'.self::Both->value),
        ];
    }

    public function label(): string
    {
        return __('transport.direction_enum.'.$this->value);
    }

    public function coversThere(): bool
    {
        return $this === self::There || $this === self::Both;
    }

    public function coversBack(): bool
    {
        return $this === self::Back || $this === self::Both;
    }

    public function overlaps(self $other): bool
    {
        return ($this->coversThere() && $other->coversThere())
            || ($this->coversBack() && $other->coversBack());
    }
}
