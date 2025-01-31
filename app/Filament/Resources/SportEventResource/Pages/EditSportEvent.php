<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Filament\Resources\SportEventResource;
use App\Services\OrisApiService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSportEvent extends EditRecord
{
    protected static string $resource = SportEventResource::class;

    /**
     * @return array{Action|null}
     */
    protected function getHeaderActions(): array
    {
        return [
            $this->showUpdateEventFromOris(),
            DeleteAction::make(),
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
