<?php

declare(strict_types=1);

namespace App\Enums;

enum EntryStatus: string
{
    case Created = 'created';
    case Edited = 'edited';
    case Deleted = 'deleted';

    public static function enumArray(): array
    {
        return [
            'deleted' => self::Deleted,
            'created' => self::Created,
            'edited' => self::Edited,
        ];
    }
}
