<?php

declare(strict_types=1);

namespace App\Services\Bank\Connector;

use App\Models\BankAccount;
use Carbon\Carbon;

interface ConnectorInterface
{
    public function getTransactions(BankAccount $bankAccount, ?Carbon $fromDate = null): ?array;
}
