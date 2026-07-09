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
                        ->title('Poznámku jsme uložili')
                        ->body('Děkujeme za zaslání dotazu k vyúčtování, pokusíme se to vyřešit.')
                        ->success()
                        ->seconds(8)
                        ->send();

                    $notificationUsers = User::role([AppRoles::BillingSpecialist->value, AppRoles::SuperAdmin->value])->get();
                    foreach ($notificationUsers as $recipient) {
                        Notification::make()
                            ->title('Poznámka k vyúčtování')
                            ->body('Uživatel: ' . Auth::user()?->name . ' | Vyúčtování ID: ' . $record->id)
                            ->sendToDatabase($recipient);
                    }
                }
            })
            ->color('gray')
            ->label('Poznámka')
            ->icon('heroicon-m-pencil-square')
            ->modalHeading('Poznámka k platbě')
            ->modalDescription('Pokud není něco v pořádků, sem prosím napiš důvody jak to je jinak. Prosím stručně a věcně.')
            ->modalSubmitActionLabel('Uložit poznámku')
            ->modalContentFooter(fn (UserCredit $record) => view('filament.modals.user-add-credit-admin', [
                'record' => $record,
            ]))
            ->schema([
                MarkdownEditor::make('user_note')
                    ->label('Poznámka'),
            ]);
    }
}
