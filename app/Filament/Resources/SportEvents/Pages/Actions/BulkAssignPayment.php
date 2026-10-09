<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions;

use App\Enums\AppRoles;
use App\Enums\UserCreditSource;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Models\SportEvent;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use App\Models\UserRaceProfile;
use App\Shared\Helpers\EmptyType;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BulkAssignPayment
{
    public static function make(SportEvent $sportEvent): BulkAction
    {
        return BulkAction::make('assignEventPayment')
            ->label(__('sport-event.actions.assign_payment.label'))
            ->icon('heroicon-o-banknotes')
            ->color('primary')
            ->modalHeading(__('sport-event.actions.assign_payment.modal_heading'))
            ->modalDescription(__('sport-event.actions.assign_payment.modal_description'))
            ->modalSubmitActionLabel(__('sport-event.actions.assign_payment.modal_submit'))
            ->visible(fn (): bool => Auth::user()?->hasRole([AppRoles::BillingSpecialist, AppRoles::SuperAdmin]) ?? false)
            ->schema([
                TextInput::make('amount')
                    ->label(__('sport-event.actions.assign_payment.amount'))
                    ->numeric()
                    ->minValue(0.01)
                    ->required()
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('sport-event.actions.assign_payment.amount_hint')),
                MarkdownEditor::make('note')
                    ->label(__('sport-event.actions.assign_payment.note'))
                    ->hint(__('sport-event.actions.assign_payment.note_hint')),
            ])
            ->action(function (Collection $records, array $data) use ($sportEvent): void {
                $created = 0;

                DB::transaction(function () use ($records, $data, $sportEvent, &$created): void {
                    foreach ($records as $raceProfile) {
                        /** @var UserRaceProfile $raceProfile */
                        $credit = new UserCredit();
                        $credit->user_id = $raceProfile->user_id;
                        $credit->user_race_profile_id = $raceProfile->id;
                        $credit->sport_event_id = $sportEvent->id;
                        $credit->amount = -(float) $data['amount'];
                        $credit->currency = UserCredit::CURRENCY_CZK;
                        $credit->credit_type = UserCreditType::CashOut;
                        $credit->source = UserCreditSource::User->value;
                        $credit->source_user_id = Auth::user()?->id;
                        $credit->status = UserCreditStatus::Done;
                        $credit->saveOrFail();

                        if (! EmptyType::stringEmpty($data['note'] ?? '')) {
                            $note = new UserCreditNote();
                            $note->user_credit_id = $credit->id;
                            if (Auth::user()?->id !== null) {
                                $note->note_user_id = Auth::user()->id;
                            }
                            $note->note = $data['note'];
                            $note->internal = false;
                            $note->saveOrFail();
                        }

                        $created++;
                    }
                });

                Notification::make()
                    ->title(__('sport-event.actions.assign_payment.notification_title'))
                    ->body(__('sport-event.actions.assign_payment.notification_body', ['count' => $created]))
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

}
