<?php

declare(strict_types=1);

namespace App\Services\Bank\MatchRules;

use App\Services\Bank\Enums\CompareType;
use App\Services\Bank\Enums\TransactionIndicator;

class ExtraMembershipFeesRule implements RuleInterface
{
    public function getRule(): CompareRule
    {
        return new CompareRule(
            transactionIndicator: TransactionIndicator::Credit,
            compareType: CompareType::ExtraMembershipFees,
            variablePrefix: config('site-config.club.extra_membership_fees_prefix'),
        );
    }
}
