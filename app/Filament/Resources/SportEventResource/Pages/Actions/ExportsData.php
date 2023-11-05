<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages\Actions;

use App\Enums\AppRoles;
use App\Http\Controllers\UserEntryController;
use App\Models\SportEvent;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Pages\Actions\Action as ModalAction;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Filament\Notifications\Notification;

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
            ->action(function (array $data): Response|BinaryFileResponse|null {
                /** @var SportEvent $sportEvent */

                if ($data['export_type'] === 'userEntry') {

                    Notification::make()
                        ->title('Export přihlášek proběhl v pořádku')
                        ->body('Souboru excelu přihlášených uživatelů otevřete z disku.')
                        ->success()
                        ->seconds(15)
                        ->send();

                    return (new UserEntryController())->export($this->sportEvent->id);
                } else {
                    return null;
                }
            })
            ->color('secondary')
            ->label('Export')
            ->icon('heroicon-o-document-download')
            ->modalHeading('Vytvoří export podle zadání')
            ->modalSubheading('Zvol požadovaný export.')
            ->modalButton('Exportovat')
            ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin->value, AppRoles::EventMaster->value]))
            ->form([
                Grid::make(1)
                    ->schema([
                        Select::make('export_type')
                            ->label('Nabízené exporty')
                            ->options([
                                'userEntry' => 'Aktuální přihlášky ve formátu *.xlsx',
                            ])
                            ->required(),
                    ]),

            ]);
    }
}