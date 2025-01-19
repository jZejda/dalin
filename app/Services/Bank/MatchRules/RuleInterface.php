<?php

declare(strict_types=1);

namespace App\Services\Bank\MatchRules;

interface RuleInterface
{
    public function getRule(): CompareRule;
}
