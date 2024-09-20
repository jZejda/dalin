<?php

declare(strict_types=1);

namespace App\Services\Bank\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Override;

enum TransactionIndicator: string implements HasColor, HasIcon, HasLabel
{
    case Debit = 'debit';
    case Credit = 'credit';

    #[Override]
    public function getLabel(): ?string
    {
        $trKey = 'bank_transaction.transaction_indicator.';

        return __($trKey.$this->value);
    }

    #[Override]
    public function getIcon(): ?string
    {
        return match ($this) {
            self::Debit => 'heroicon-m-arrow-trending-down',
            self::Credit => 'heroicon-m-arrow-trending-up',
        };
    }

    #[Override]
    public function getColor(): ?string
    {
        return match ($this) {
            self::Debit => 'danger',
            self::Credit => 'success',
        };
    }

    public static function enumArray(): array
    {
        $trKey = 'bank-transaction.transaction_indicator.';

        return [
            self::Credit->value => __($trKey.self::Credit->value),
            self::Debit->value => __($trKey.self::Debit->value),
        ];
    }
}
