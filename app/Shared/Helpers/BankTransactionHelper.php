<?php

declare(strict_types=1);

namespace App\Shared\Helpers;

use App\Models\BankTransaction;

class BankTransactionHelper
{
    public static function hasTransactionUserCredit(BankTransaction $transaction): bool
    {
        return $transaction->userCredit()->exists();
    }

    public static function compareVariableSymbol(string $variableSymbol, string $toCompare, bool $strict = false): bool
    {
        if ($strict) {
            if ($variableSymbol === $toCompare) {
                return true;
            }
            return false;
        }

        $variableSymbolWithoutZeros = ltrim($variableSymbol, '0');
        if ($variableSymbolWithoutZeros === $toCompare) {
            return true;
        }

        return false;
    }
}
