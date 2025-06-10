<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\UserCreditType;
use App\Filament\Resources\UserCreditResource\Actions\AddUserTransferBillingModal;
use App\Filament\Widgets\UserCreditBalance;
use App\Filament\Widgets\UserSendCreditInfo;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\ActionGroup;
use Filament\Pages\Page;

class UserCreditList extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $title = 'Finance';
    protected static ?string $navigationGroup = 'Uživatel';
    protected static ?int $navigationSort = 34;

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
            ->label('Nový záznam')
        ];
    }

    protected static string $view = 'filament.pages.user-credit-list';
}
