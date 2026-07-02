<?php

declare(strict_types=1);

namespace App\Enums;

enum MailSource: string
{
    case User = 'user';
    case Cron = 'cron';
    case System = 'system';

    public function label(): string
    {
        return __('mail-log.source_enum.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::User => 'success',
            self::Cron => 'info',
            self::System => 'gray',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::User => 'heroicon-o-user',
            self::Cron => 'heroicon-o-clock',
            self::System => 'heroicon-o-cog-6-tooth',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function enumArray(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
