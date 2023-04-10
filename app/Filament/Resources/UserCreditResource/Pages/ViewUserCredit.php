<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserCreditResource\Pages;

use App\Filament\Resources\UserCreditResource;
use App\Filament\Resources\UserCreditResource\Widgets\UserCreditChat;
use App\Models\UserCreditNote;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserCredit extends ViewRecord
{
    protected static string $resource = UserCreditResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            $this->createNewNote(),
        ];
    }

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserCreditChat::class,
        ];
    }

    protected function createNewNote(): ?Action
    {
        return Action::make('createNewNote')
            ->action(function (array $data): void {

                $userCreditNote = new UserCreditNote();
                $userCreditNote->user_credit_id = $this->record->id;
                $userCreditNote->note_user_id = auth()->user()->id;
                $userCreditNote->note = $data['user_note'];

                if($userCreditNote->save()) {
                    Notification::make()
                        ->title('Poznámku jsme uložili')
                        ->body('Děkujeme za zaslání dotazu k vyúčtování, pokusíme se to vyřešit.')
                        ->success()
                        ->seconds(8)
                        ->send();
                }
            })

            ->color('secondary')
            ->label('Poznámka')
            ->icon('heroicon-s-pencil-alt')
            ->modalHeading('Poznámka k platbě')
            ->modalSubheading('Pokud není něco v pořádků, sem prosím napiš důvody jak to je jinak. Prosím stručně a věcně.')
            ->modalButton('Uložit poznámku')
            //->visible(auth()->user()->hasRole(['super_admin', 'event_master']))
            ->form([
                MarkdownEditor::make('user_note')
                    ->label('Poznámka')
            ]);
    }
}