<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserCreditResource\Actions;

use App\Enums\AppRoles;
use App\Enums\UserCreditSource;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Models\BankTransaction;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use App\Shared\Helpers\AppHelper;
use App\Shared\Helpers\EmptyType;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class AddUserTransportBillingModal
{
    public function getAction(): Action
    {
        return Action::make('addUserTransportBilling')
            ->action(function (array $data): void {
                $userCreditTo = new UserCredit();
                $userCreditTo->amount = $data['amount'];
                $userCreditTo->currency = UserCredit::CURRENCY_CZK;
                $userCreditTo->user_id = (integer)$data['user_id'];
                $userCreditTo->related_user_id = (integer)$data['related_user_id'];
                $userCreditTo->source_user_id = Auth()->user()?->id;
                $userCreditTo->sport_event_id = (integer)$data['sport_event_id'];
                $userCreditTo->credit_type = UserCreditType::TransferCreditBetweenUsers;
                $userCreditTo->source = UserCreditSource::User->value;
                $userCreditTo->status = UserCreditStatus::Done;
                $userCreditTo->saveOrFail();

                $userCreditFrom = new UserCredit();
                $userCreditFrom->amount = -$data['amount'];
                $userCreditFrom->currency = UserCredit::CURRENCY_CZK;
                $userCreditFrom->user_id = (integer)$data['related_user_id'];
                $userCreditFrom->related_user_id = (integer)$data['user_id'];
                $userCreditFrom->source_user_id = Auth()->user()?->id;
                $userCreditFrom->sport_event_id = (integer)$data['sport_event_id'];
                $userCreditFrom->credit_type = UserCreditType::TransferCreditBetweenUsers;
                $userCreditFrom->source = UserCreditSource::User->value;
                $userCreditFrom->status = UserCreditStatus::Done;
                $userCreditFrom->saveOrFail();

                if (!EmptyType::stringEmpty($data['note'])) {
                    $userToCreditNotes = new UserCreditNote();
                    $userToCreditNotes->user_credit_id = $userCreditTo->id;
                    $userToCreditNotes->note = $data['note'];
                    if (Auth()->user()?->id !== null) {
                        $userToCreditNotes->note_user_id = Auth()->user()->id;
                    }
                    $userToCreditNotes->internal = false;
                    $userToCreditNotes->saveOrFail();

                    $userFromCreditNotes = new UserCreditNote();
                    $userFromCreditNotes->user_credit_id = $userCreditFrom->id;
                    $userFromCreditNotes->note = $data['note'];
                    if (Auth()->user()?->id !== null) {
                        $userFromCreditNotes->note_user_id = Auth()->user()->id;
                    }
                    $userFromCreditNotes->internal = false;
                    $userFromCreditNotes->saveOrFail();
                }

                Notification::make()
                    ->title('Cestovní vyúčtování')
                    ->body('Cestovný vyúčtování bylo přiřazeno uživatelům')
                    ->success()
                    ->send();

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
                        ->disabled()
                        ->options([
                            UserCredit::CURRENCY_CZK => UserCredit::CURRENCY_CZK,
                            UserCredit::CURRENCY_EUR => UserCredit::CURRENCY_EUR,
                        ])
                        ->required(),
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
                MarkdownEditor::make('note')
                    ->label(__('user-credit.note')),
                Select::make('sport_event_id')
                    ->label(__('user-credit.event_name'))
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'Není potřeba doplňovat.')
                    ->options(
                        SportEvent::all()
                            ->where('date', '>', Carbon::now()->subMonths(12)->format(AppHelper::MYSQL_DATE_TIME))
                            ->sortByDesc('date')
                            ->pluck('sport_event_oris_compact_title', 'id')
                    )->searchable(),
            ]);

    }

}
