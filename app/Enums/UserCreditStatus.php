<?php

declare(strict_types=1);

namespace App\Enums;

enum UserCreditStatus: string
{
    case Done = 'done';
    case UnAssign = 'un_assign';
    case Open = 'open';

}
