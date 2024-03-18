<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Override;

enum UserCreditType: string implements HasIcon, HasColor
{
    case CacheOut = 'cacheOut';
    case UserDonation = 'userDonation';
    case MembershipFees = 'membershipFees';
    case TransferCreditBetweenUsers = 'transferCreditBetweenUsers';
    case InitialDeposit = 'initialDeposit';

    public static function enumArray(): array
    {
        $trKey = 'sport-event.type_enum_credit_type.';
        return [
            'cacheOut'      => __($trKey . self::CacheOut->value),
            'userDonation'  => __($trKey . self::UserDonation->value),
            'membershipFees'  => __($trKey . self::MembershipFees->value),
            'transferCreditBetweenUsers'  => __($trKey . self::TransferCreditBetweenUsers->value),
            'initialDeposit'  => __($trKey . self::InitialDeposit->value),
        ];
    }

    #[Override] public function getIcon(): ?string
    {
        return match ($this) {
            self::CacheOut => 'heroicon-m-arrow-trending-down',
            self::UserDonation => 'heroicon-m-arrow-trending-up',
            self::MembershipFees => 'heroicon-m-banknotes',
            self::TransferCreditBetweenUsers => 'heroicon-m-arrows-right-left',
            self::InitialDeposit => 'heroicon-m-plus-circle',
        };
    }

    #[Override] public function getColor(): ?string
    {
        return match ($this) {
            self::CacheOut => 'danger',
            self::UserDonation => 'success',
            self::MembershipFees, self::InitialDeposit => 'gray',
            self::TransferCreditBetweenUsers => 'warning',
        };
    }


}
