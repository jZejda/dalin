<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\UserCreditType;
use App\Filament\Resources\UserCredits\Actions\AddUserTransferBillingModal;
use App\Filament\Widgets\UserCreditBalance;
use App\Filament\Widgets\UserSendCreditInfo;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\ActionGroup;
use Filament\Pages\Page;

class UserCreditList extends Page
{
    use HasPageShield;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 34;

    public function getTitle(): string
    {
        return __('user-credit.list.page_title');
    }

    public static function getNavigationLabel(): string
    {
        return __('user-credit.list.navigation_label');
    }

    public static function getNavigationGroup(): string | \UnitEnum | null
    {
        return __('app.navigation_groups.users');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserCreditBalance::class,
            UserSendCreditInfo::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make(
                [
                (new AddUserTransferBillingModal())->getAction(
                    AddUserTransferBillingModal::ACTION_ADD_USER_TRANSPORT_BILLING,
                    UserCreditType::TransportBilling
                ),
                (new AddUserTransferBillingModal())->getAction(
                    AddUserTransferBillingModal::ACTION_ADD_USER_TRANSFER_BILLING,
                    UserCreditType::TransferCreditBetweenUsers
                ),
            ]
            )->button()
            ->icon('heroicon-o-plus-circle')
            ->color('gray')
            ->label(__('user-credit.list.new_record_label'))
        ];
    }

    protected string $view = 'filament.pages.user-credit-list';
}
