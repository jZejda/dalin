<?php

declare(strict_types=1);

namespace App\Filament\Resources\MemberFinances\Actions;

use App\Enums\UserCreditSource;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use App\Shared\Helpers\EmptyType;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;

class AddBulkCreditAction
{
    public static function make(): BulkAction
    {
        return BulkAction::make('addBulkCredit')
            ->label(__('member-finance.actions.bulk_credit.label'))
            ->icon('heroicon-o-banknotes')
            ->color('primary')
            ->modalHeading(__('member-finance.actions.bulk_credit.modal_heading'))
            ->modalDescription(__('member-finance.actions.bulk_credit.modal_description'))
            ->modalSubmitActionLabel(__('member-finance.actions.bulk_credit.modal_submit'))
            ->schema([
                Select::make('credit_type')
                    ->label(__('member-finance.actions.bulk_credit.field_type'))
                    ->options(self::getAllowedCreditTypes())
                    ->required()
                    ->default(UserCreditType::MembershipFees->value),
                TextInput::make('amount')
                    ->label(__('member-finance.common.amount'))
                    ->numeric()
                    ->required()
                    ->minValue(0.01)
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('member-finance.common.amount_tooltip_bulk')),
                MarkdownEditor::make('note')
                    ->label(__('member-finance.common.note'))
                    ->hint(__('member-finance.common.note_optional')),
            ])
            ->action(function (Collection $records, array $data): void {
                $created = 0;

                foreach ($records as $user) {
                    /** @var User $user */
                    $credit = new UserCredit();
                    $credit->user_id = $user->id;
                    $credit->amount = (float) $data['amount'];
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

                    $created++;
                }

                Notification::make()
                    ->title(__('member-finance.actions.bulk_credit.notification_title'))
                    ->body(__('member-finance.actions.bulk_credit.notification_body', ['count' => $created]))
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    private static function getAllowedCreditTypes(): array
    {
        $trKey = 'sport-event.type_enum_credit_type.';

        return [
            UserCreditType::MembershipFees->value  => __(($trKey) . UserCreditType::MembershipFees->value),
            UserCreditType::UserDonation->value     => __(($trKey) . UserCreditType::UserDonation->value),
            UserCreditType::CashOut->value          => __(($trKey) . UserCreditType::CashOut->value),
            UserCreditType::InitialDeposit->value   => __(($trKey) . UserCreditType::InitialDeposit->value),
            UserCreditType::TransportBilling->value => __(($trKey) . UserCreditType::TransportBilling->value),
        ];
    }
}
