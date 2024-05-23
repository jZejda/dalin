<?php

declare(strict_types=1);

namespace App\Services\Bank\Enums;

enum TransactionIndicator: string
{
    case DEBIT = 'debit';
    case CREDIT = 'credit';
}
