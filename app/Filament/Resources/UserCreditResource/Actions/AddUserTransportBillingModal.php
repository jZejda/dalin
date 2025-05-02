<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserCreditResource\Actions;

use App\Enums\AppRoles;
use App\Enums\UserCreditSource;
use App\Enums\UserCreditType;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserCredit;
use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class AddUserTransportBillingModal
{
    public function getAction(): Action
    {
        return Action::make('addUserTransportBilling')
            ->action(function (array $data): void {

                dd($data);

            })
            ->color('gray')
            ->label(__('user-credit.actions.transport_billing.action_group_label'))
            ->icon('heroicon-o-truck')
            ->modalHeading(__('user-credit.actions.transport_billing.modal_heading'))
            ->modalDescription(__('user-credit.actions.transport_billing.modal_description'))
            ->modalSubmitActionLabel(__('user-credit.actions.transport_billing.modal_submit_action_label'))
            ->visible(
                auth()->user() !== null && auth()->user()->hasRole([AppRoles::SuperAdmin, AppRoles::EventMaster])
            )
            ->form([
                // Credit detail
                Grid::make()->schema([
                    Select::make('credit_type')
                        ->label(__('user-credit.form.type_title'))
                        ->options(UserCreditType::enumArray())
                        ->debounce()
                        ->default(UserCreditType::TransportBilling)
                        ->disabled()
                        ->required()
                        ->live(),
                    TextInput::make('amount')
                        ->label(__('user-credit.form.amount_title'))
                        ->numeric()
                        ->minValue(1)
                        ->hintColor('default')
                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Je možné vložit pouze kladné hodnoty.')
                        ->required(),
                    Select::make('currency')
                        ->label(__('user-credit.form.currency_title'))
                        ->default(UserCredit::CURRENCY_CZK)
                        ->options([
                            UserCredit::CURRENCY_CZK => UserCredit::CURRENCY_CZK,
                            UserCredit::CURRENCY_EUR => UserCredit::CURRENCY_EUR,
                        ])
                        ->required()
                        ->disabled(),
                ])->columns(3),

                // Users
                Grid::make()->schema([
                    Select::make('user_id')
                        ->label('Připsat částku uživateli')
                        ->options(User::all()->pluck('user_identification', 'id'))
                        ->hintColor('warning')
                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Částka bude připsána na konto tohoto uživtele.')
                        ->required()
                        ->searchable(),
                    Select::make('related_user_id')
                        ->label('Strhnout částku uživateli')
                        ->options(User::all()->pluck('user_identification', 'id'))
                        ->hintColor('warning')
                        ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Zvolená částka bude stržena tomuto uživateli.')
                        ->required()
                        ->searchable(),
                ])->columns(2),

                Select::make('sport_event_id')
                    ->label(__('user-credit.event_name'))
                    ->options(
                        SportEvent::all()
                            ->where('date', '>', Carbon::now()->subMonths(12)->format(AppHelper::MYSQL_DATE_TIME))
                            ->sortByDesc('date')
                            ->pluck('sport_event_oris_compact_title', 'id')
                    )->searchable(),
                Select::make('source')
                    ->label(__('user-credit.form.source_title'))
                    ->default(UserCreditSource::User->value)
                    ->options(UserCreditSource::enumArray())
                    ->disabled()
                    ->required(),
            ]);

    }

}
