<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Override;

enum SportEventFilters: string implements HasIcon
{
    case CaseOne = 'caseOne';
    case CaseTwo = 'caseTwo';
    case CaseThree = 'caseThree';
    case CaseFour = 'caseFour';
    case CaseFlag = 'caseFlag';
    case CaseCircle = 'caseCircle';

    public static function enumArray(): array
    {
        return [
            'race' => __('sport-event.type_enum.'.self::Race->value),
            'training' => __('sport-event.type_enum.'.self::Training->value),
            'trainingCamp' => __('sport-event.type_enum.'.self::TrainingCamp->value),
            'other' => __('sport-event.type_enum.'.self::Other->value),
        ];
    }

    #[Override]
    public function getIcon(): string
    {
        return match ($this) {
            self::CaseOne => 'heroicon-m-arrow-trending-down',
            self::CaseTwo => 'heroicon-m-arrow-trending-up',
            self::CaseThree => 'heroicon-m-banknotes',
            self::CaseFour => 'heroicon-o-truck',
            self::CaseFlag => 'heroicon-m-plus-circle',
            self::CaseCircle => 'heroicon-m-plus-circle',
        };
    }
}
