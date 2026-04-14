<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions;

use Filament\Actions\Action;
use Filament\Schemas\Components\Grid;
use App\Enums\AppRoles;
use App\Http\Controllers\UserEntryController;
use App\Models\SportEvent;
use Filament\Forms\Components\Select;
use Filament\Actions\Action as ModalAction;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Filament\Notifications\Notification;
use Livewire\Features\SupportRedirects\Redirector as LivewireRedirector;

class ExportsData
{
    private SportEvent $sportEvent;

    public function __construct(SportEvent $sportEvent)
    {
        $this->sportEvent = $sportEvent;
    }

    public function makeExport(): Action
    {
        return ModalAction::make('makeExport')
            ->action(function (array $data, ModalAction $action): Response|BinaryFileResponse|RedirectResponse|LivewireRedirector|null {
                /** @var SportEvent $sportEvent */

                if ($data['export_type'] === 'userEntryXlsx') {

                    Notification::make()
                        ->title('Export přihlášek proběhl v pořádku')
                        ->body('Souboru excelu přihlášených uživatelů otevřete z disku.')
                        ->success()
                        ->seconds(15)
                        ->send();

                    $action->close();
                    return (new UserEntryController())->exportXlsx($this->sportEvent->id);
                } elseif ($data['export_type'] === 'IofV3EntryList') {
                    Notification::make()
                        ->title('Export přihlášek proběhl v pořádku')
                        ->body('Souboru xml iof v3 přihlášených uživatelů otevřete z disku.')
                        ->success()
                        ->seconds(15)
                        ->send();

                    $action->close();
                    return response()->redirectTo(route('admin.export.event-entry-iof', ['eventId' => $this->sportEvent->id]));
                } elseif ($data['export_type'] === 'CSOS') {
                    Notification::make()
                        ->title('Export přihlášek proběhl v pořádku')
                        ->body('Souboru txt CSOS přihlášených uživatelů otevřete z disku.')
                        ->success()
                        ->seconds(15)
                        ->send();

                    $action->close();
                    return response()->redirectTo(route('admin.export.event-entry-csos', ['eventId' => $this->sportEvent->id]));
                } else {
                    return null;
                }
            })
            ->color('gray')
            ->label('Export')
            ->icon('heroicon-o-document-arrow-down')
            ->modalHeading('Vytvoří export podle zadání')
            ->modalDescription('Zvol požadovaný export.')
            ->modalSubmitActionLabel('Exportovat')
            ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin, AppRoles::EventMaster, AppRoles::EventOrganizer]))
            ->schema([
                Grid::make(1)
                    ->schema([
                        Select::make('export_type')
                            ->label('Nabízené exporty')
                            ->options([
                                'userEntryXlsx' => 'Přihlášky | Excel (*.xlsx)',
                                'IofV3EntryList' => 'Přihlášky | IOF XML v3 (*.xml) - EXPERIMENTAL',
                                'CSOS' => 'Přihlášky | ČSOS (*.txt)',
                            ])
                            ->required(),
                    ]),

            ]);
    }
}
