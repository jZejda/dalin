<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Override;

enum MarketPaymentMethod: string implements HasColor, HasIcon, HasLabel
{
    case DirectPayment = 'directPayment';
    case CreditCharge = 'creditCharge';

    /** @return array<string, string> */
    public static function enumArray(): array
    {
        return [
            self::DirectPayment->value => __('marketplace.payment_method_enum.'.self::DirectPayment->value),
            self::CreditCharge->value => __('marketplace.payment_method_enum.'.self::CreditCharge->value),
        ];
    }

    #[Override]
    public function getLabel(): string
    {
        return __('marketplace.payment_method_enum.'.$this->value);
    }

    #[Override]
    public function getColor(): string
    {
        return match ($this) {
            self::DirectPayment => 'info',
            self::CreditCharge => 'success',
        };
    }

    #[Override]
    public function getIcon(): string
    {
        return match ($this) {
            self::DirectPayment => 'heroicon-o-banknotes',
            self::CreditCharge => 'heroicon-o-credit-card',
        };
    }
}
