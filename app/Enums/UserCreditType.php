<?php

declare(strict_types=1);

namespace App\Enums;

enum UserCreditType: string
{
    case CacheOut = 'cacheOut';
    case UserDonation = 'userDonation';
    case MembershipFees = 'membershipFees';

    public static function enumArray(): array
    {
        $trKey = 'sport-event.type_enum_credit_type.';
        return [
            'cacheOut'      => __($trKey . self::CacheOut->value),
            'userDonation'  => __($trKey . self::UserDonation->value),
            'membershipFees'  => __($trKey . self::MembershipFees->value),
        ];
    }
}
