<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Override;

enum UserCreditType: string implements HasColor, HasIcon
{
    case CashOut = 'cashOut';
    case UserDonation = 'userDonation';
    case MembershipFees = 'membershipFees';
    case TransferCreditBetweenUsers = 'transferCreditBetweenUsers';
    case InitialDeposit = 'initialDeposit';
    case TransportBilling = 'transportBilling';
    case ServiceFee = 'serviceFee';
    case MarketplaceBilling = 'marketplaceBilling';
    case Accommodation = 'accommodation';
    case Meals = 'meals';

    public static function enumArray(): array
    {
        $trKey = 'sport-event.type_enum_credit_type.';

        return [
            'cashOut' => __($trKey.self::CashOut->value),
            'userDonation' => __($trKey.self::UserDonation->value),
            'membershipFees' => __($trKey.self::MembershipFees->value),
            'transferCreditBetweenUsers' => __($trKey.self::TransferCreditBetweenUsers->value),
            'initialDeposit' => __($trKey.self::InitialDeposit->value),
            'transportBilling' => __($trKey.self::TransportBilling->value),
            'serviceFee' => __($trKey.self::ServiceFee->value),
            'marketplaceBilling' => __($trKey.self::MarketplaceBilling->value),
            'accommodation' => __($trKey.self::Accommodation->value),
            'meals' => __($trKey.self::Meals->value),
        ];
    }

    #[Override]
    public function getIcon(): string
    {
        return match ($this) {
            self::CashOut => 'heroicon-m-arrow-trending-down',
            self::UserDonation => 'heroicon-m-arrow-trending-up',
            self::MembershipFees => 'heroicon-m-banknotes',
            self::TransferCreditBetweenUsers => 'heroicon-o-truck',
            self::InitialDeposit => 'heroicon-m-plus-circle',
            self::TransportBilling => 'heroicon-o-truck',
            self::ServiceFee => 'heroicon-m-shopping-bag',
            self::MarketplaceBilling => 'heroicon-m-shopping-cart',
            self::Accommodation => 'heroicon-o-home',
            self::Meals => 'heroicon-o-cake',
        };
    }

    #[Override]
    public function getColor(): string
    {
        return match ($this) {
            self::CashOut, self::ServiceFee, self::MarketplaceBilling, self::Accommodation, self::Meals => 'danger',
            self::UserDonation, self::TransferCreditBetweenUsers => 'success',
            self::MembershipFees, self::InitialDeposit, self::TransportBilling => 'gray',
        };
    }
}
