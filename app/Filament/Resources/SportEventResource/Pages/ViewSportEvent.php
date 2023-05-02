<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Filament\Resources\SportEventResource;
use App\Services\OrisApiService;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSportEvent extends ViewRecord
{
    protected static string $resource = SportEventResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            $this->showUpdateEventFromOris(),
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

            ->color('secondary')
            ->label('Aktualizovat závod')
            ->disabled(!$this->data['use_oris_for_entries'])
            ->icon('heroicon-s-refresh')
            ->modalHeading('Aktualizovat závod z ORISu')
            ->modalSubheading('Provede aktualizaci závodu s aktuálními daty v ORISu')
            ->modalButton('Aktualizovat')
            ->visible(auth()->user()->hasRole(['super_admin', 'event_master']))
            ->form([
            ]);
    }
}
