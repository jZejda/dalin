<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages\Actions;

use App\Enums\AppRoles;
use App\Http\Controllers\UserEntryController;
use App\Models\SportEvent;
use Filament\Forms\Components\Grid;
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

    public function makeExport(): ModalAction
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
            ->form([
                Grid::make(1)
                    ->schema([
                        Select::make('export_type')
                            ->options([
                                'userEntryXlsx' => 'Excel - Přihlášky',
                                'IofV3EntryList' => 'IOF XMLv3 - Přihlášky - EXPERIMENTAL',
                            ])
                            ->required(),
                    ]),

            ]);
    }
}
