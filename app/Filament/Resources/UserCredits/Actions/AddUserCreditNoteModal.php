<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserCredits\Actions;

use App\Enums\AppRoles;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use Filament\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class AddUserCreditNoteModal
{
    public const string ACTION_ADD_USER_CREDIT_NOTE = 'addUserCreditNote';

    public function getAction(): Action
    {
        return Action::make(self::ACTION_ADD_USER_CREDIT_NOTE)
            ->action(function (UserCredit $record, array $data): void {

                $userCreditNote = new UserCreditNote();
                $userCreditNote->user_credit_id = $record->id;
                if (Auth::user()?->id !== null) {
                    $userCreditNote->note_user_id = Auth::user()->id;
                }
                $userCreditNote->note = $data['user_note'];

                if ($userCreditNote->save()) {
                    Notification::make()
                        ->title(__('user-credit.actions.add_note.notification_saved_title'))
                        ->body(__('user-credit.actions.add_note.notification_saved_body'))
                        ->success()
                        ->seconds(8)
                        ->send();

                    $notificationUsers = User::role([AppRoles::BillingSpecialist->value, AppRoles::SuperAdmin->value])->get();
                    foreach ($notificationUsers as $recipient) {
                        Notification::make()
                            ->title(__('user-credit.actions.add_note.notification_billing_title'))
                            ->body(__('user-credit.actions.add_note.notification_billing_body', ['user' => (string) Auth::user()?->name, 'id' => (string) $record->id]))
                            ->sendToDatabase($recipient);
                    }
                }
            })
            ->color('gray')
            ->label(__('user-credit.actions.add_note.label'))
            ->icon('heroicon-m-pencil-square')
            ->modalHeading(__('user-credit.actions.add_note.modal_heading'))
            ->modalDescription(__('user-credit.actions.add_note.modal_description'))
            ->modalSubmitActionLabel(__('user-credit.actions.add_note.modal_submit_action_label'))
            ->modalContentFooter(fn (UserCredit $record) => view('filament.modals.user-add-credit-admin', [
                'record' => $record,
            ]))
            ->schema([
                MarkdownEditor::make('user_note')
                    ->label(__('user-credit.actions.add_note.note_label')),
            ]);
    }
}
