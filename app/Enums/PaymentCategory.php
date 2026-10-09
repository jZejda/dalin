<?php

declare(strict_types=1);

namespace App\Enums;

use App\Models\UserCredit;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Override;

/**
 * Business meaning of a credit journal record, derived from its credit type
 * and links (sport event, additional service). Not persisted.
 */
enum PaymentCategory: string implements HasColor, HasIcon, HasLabel
{
    case EntryFee = 'entry_fee';
    case AdditionalService = 'additional_service';
    case Transport = 'transport';
    case Marketplace = 'marketplace';
    case MembershipFee = 'membership_fee';
    case Deposit = 'deposit';
    case InitialDeposit = 'initial_deposit';
    case Transfer = 'transfer';
    case Other = 'other';

    public static function fromCredit(UserCredit $credit): self
    {
        if ($credit->sport_service_id !== null || $credit->credit_type === UserCreditType::ServiceFee) {
            return self::AdditionalService;
        }

        return match ($credit->credit_type) {
            UserCreditType::CashOut => $credit->sport_event_id !== null ? self::EntryFee : self::Other,
            UserCreditType::TransportBilling => self::Transport,
            UserCreditType::MarketplaceBilling => self::Marketplace,
            UserCreditType::MembershipFees => self::MembershipFee,
            UserCreditType::UserDonation => self::Deposit,
            UserCreditType::InitialDeposit => self::InitialDeposit,
            UserCreditType::TransferCreditBetweenUsers => self::Transfer,
        };
    }

    #[Override]
    public function getLabel(): string
    {
        return __('user-credit.payment_category_enum.'.$this->value);
    }

    #[Override]
    public function getIcon(): LucideIcon
    {
        return match ($this) {
            self::EntryFee => LucideIcon::Ticket,
            self::AdditionalService => LucideIcon::ShoppingBag,
            self::Transport => LucideIcon::Car,
            self::Marketplace => LucideIcon::Store,
            self::MembershipFee => LucideIcon::IdCard,
            self::Deposit => LucideIcon::PiggyBank,
            self::InitialDeposit => LucideIcon::Wallet,
            self::Transfer => LucideIcon::ArrowLeftRight,
            self::Other => LucideIcon::Receipt,
        };
    }

    #[Override]
    public function getColor(): string
    {
        return match ($this) {
            self::EntryFee => 'primary',
            self::AdditionalService => 'info',
            self::Transport, self::Marketplace => 'warning',
            self::Deposit, self::InitialDeposit => 'success',
            self::MembershipFee, self::Transfer, self::Other => 'gray',
        };
    }
}
