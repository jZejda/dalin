<?php

declare(strict_types=1);

namespace App\Enums;

enum EntryStatus: string
{
    case Created = 'created';
    case Edited = 'edited';
    case Deleted = 'deleted';
}
