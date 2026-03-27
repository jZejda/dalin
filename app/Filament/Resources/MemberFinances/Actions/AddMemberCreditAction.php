<?php

declare(strict_types=1);

namespace App\Filament\Resources\MemberFinances\Actions;

use App\Enums\UserCreditSource;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use App\Shared\Helpers\EmptyType;
use Filament\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class AddMemberCreditAction
{
    public static function deposit(): Action
    {
        return Action::make('addDeposit')
            ->label(__('member-finance.actions.deposit.label'))
            ->icon('heroicon-o-arrow-trending-up')
            ->color('success')
            ->modalHeading(__('member-finance.actions.deposit.modal_heading'))
            ->modalDescription(__('member-finance.actions.deposit.modal_description'))
            ->modalSubmitActionLabel(__('member-finance.actions.deposit.modal_submit'))
            ->schema([
                Select::make('credit_type')
                    ->label(__('member-finance.actions.deposit.field_type'))
                    ->options([
                        UserCreditType::UserDonation->value  => __('member-finance.actions.deposit.type_donation'),
                        UserCreditType::InitialDeposit->value => __('member-finance.actions.deposit.type_initial'),
                        UserCreditType::MembershipFees->value => __('member-finance.actions.deposit.type_membership'),
                    ])
                    ->default(UserCreditType::UserDonation->value)
                    ->required(),
                TextInput::make('amount')
                    ->label(__('member-finance.common.amount'))
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('member-finance.actions.deposit.amount_tooltip')),
                MarkdownEditor::make('note')
                    ->label(__('member-finance.common.note'))
                    ->hint(__('member-finance.common.note_optional')),
            ])
            ->action(function (array $data, $record): void {
                $credit = new UserCredit();
                $credit->user_id = $record->id;
                $credit->amount = abs((float) $data['amount']);
                $credit->currency = UserCredit::CURRENCY_CZK;
                $credit->credit_type = UserCreditType::from($data['credit_type']);
                $credit->source = UserCreditSource::User->value;
                $credit->source_user_id = auth()->user()?->id;
                $credit->status = UserCreditStatus::Done;
                $credit->saveOrFail();

                if (! EmptyType::stringEmpty($data['note'] ?? '')) {
                    $note = new UserCreditNote();
                    $note->user_credit_id = $credit->id;
                    if (auth()->user()?->id !== null) {
                        $note->note_user_id = auth()->user()->id;
                    }
                    $note->note = $data['note'];
                    $note->internal = false;
                    $note->saveOrFail();
                }

                Notification::make()
                    ->title(__('member-finance.actions.deposit.notification_title'))
                    ->body(__('member-finance.actions.deposit.notification_body'))
                    ->success()
                    ->send();
            });
    }

    public static function deduct(): Action
    {
        return Action::make('addDeduction')
            ->label(__('member-finance.actions.deduct.label'))
            ->icon('heroicon-o-arrow-trending-down')
            ->color('danger')
            ->modalHeading(__('member-finance.actions.deduct.modal_heading'))
            ->modalDescription(__('member-finance.actions.deduct.modal_description'))
            ->modalSubmitActionLabel(__('member-finance.actions.deduct.modal_submit'))
            ->schema([
                Select::make('credit_type')
                    ->label(__('member-finance.actions.deduct.field_type'))
                    ->options([
                        UserCreditType::CashOut->value        => __('member-finance.actions.deduct.type_cashout'),
                        UserCreditType::MembershipFees->value => __('member-finance.actions.deduct.type_membership'),
                        UserCreditType::TransportBilling->value => __('member-finance.actions.deduct.type_transport'),
                    ])
                    ->default(UserCreditType::CashOut->value)
                    ->required(),
                TextInput::make('amount')
                    ->label(__('member-finance.common.amount'))
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('member-finance.actions.deduct.amount_tooltip')),
                MarkdownEditor::make('note')
                    ->label(__('member-finance.common.note'))
                    ->hint(__('member-finance.common.note_optional')),
            ])
            ->action(function (array $data, $record): void {
                $credit = new UserCredit();
                $credit->user_id = $record->id;
                $credit->amount = -abs((float) $data['amount']);
                $credit->currency = UserCredit::CURRENCY_CZK;
                $credit->credit_type = UserCreditType::from($data['credit_type']);
                $credit->source = UserCreditSource::User->value;
                $credit->source_user_id = auth()->user()?->id;
                $credit->status = UserCreditStatus::Done;
                $credit->saveOrFail();

                if (! EmptyType::stringEmpty($data['note'] ?? '')) {
                    $note = new UserCreditNote();
                    $note->user_credit_id = $credit->id;
                    if (auth()->user()?->id !== null) {
                        $note->note_user_id = auth()->user()->id;
                    }
                    $note->note = $data['note'];
                    $note->internal = false;
                    $note->saveOrFail();
                }

                Notification::make()
                    ->title(__('member-finance.actions.deduct.notification_title'))
                    ->body(__('member-finance.actions.deduct.notification_body'))
                    ->success()
                    ->send();
            });
    }
}
