<?php

declare(strict_types=1);

namespace App\Shared\Helpers;

use App\Models\User;

class UserHelper
{
    public static function getUserByVariableSymbol(string $variableSymbol): ?User
    {
        $userVs = null;
        $membershipFeesPrefix = config('site-config.club.extra_membership_fees_prefix');

        $pos = strpos($variableSymbol, $membershipFeesPrefix);
        if ($pos !== false) {
            $userVs = substr($variableSymbol, $pos + strlen($membershipFeesPrefix));
        }

        $users = User::query()
            ->where('payer_variable_symbol', '=', $userVs)
            ->get();

        if (count($users) > 1) {
            return null;
        } else {
            return $users->first();
        }
    }

    public static function validateUserVariableSymbol(string $variableSymbol): bool
    {
        $membershipFeesPrefix = config('site-config.club.extra_membership_fees_prefix');
        if (preg_match('/^'. $membershipFeesPrefix .'\d{4}$/', $variableSymbol)) {
            return true;
        }

        return false;
    }
}
