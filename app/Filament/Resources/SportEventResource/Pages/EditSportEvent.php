<?php

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Filament\Resources\SportEventResource;
use App\Filament\Resources\SportEventResource\Widgets\EventEditMap;
use App\Services\OrisApiService;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSportEvent extends EditRecord
{
    protected static string $resource = SportEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->showUpdateEventFromOris(),
            Actions\DeleteAction::make(),
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // EventEditMap::class,
        ];
    }

    protected function showUpdateEventFromOris(): ?Action
    {
        return Action::make('updateSportEvent')
            ->action(function (array $data): void {

                $result = (new OrisApiService())->updateEvent($this->data['oris_id']);

                if ($result) {
                    Notification::make()
                        ->title('Aktualizace závodu')
                        ->body('Závod byl úspěšně aktualizován')
                        ->success()
                        ->seconds(8)
                        ->send();
                } else {
                    Notification::make()
                        ->title('Aktualizace závodu')
                        ->body('Něco se nepovedlo, Můžeš vyzkoušet akci zopakovat nebo kontaktuj admina s popisem chyby, děkujeme.')
                        ->danger()
                        ->send();
                }
            })

            ->color('gray')
            ->label('Aktualizovat závod')
            ->disabled((!$this->data['use_oris_for_entries'] && is_null($this->data['oris_id'])))
            ->icon('heroicon-m-arrow-path')
            ->modalHeading('Aktualizovat závod z ORISu')
            ->modalDescription('Provede aktualizaci závodu s aktuálními daty v ORISu')
            ->modalSubmitActionLabel('Aktualizovat')
            ->visible(auth()->user()->hasRole(['super_admin', 'event_master']))
            ->form([
            ]);
    }
}
