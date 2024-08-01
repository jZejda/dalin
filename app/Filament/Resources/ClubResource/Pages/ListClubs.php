<?php

declare(strict_types=1);

namespace App\Filament\Resources\ClubResource\Pages;

use App\Filament\Resources\ClubResource;
use App\Services\OrisApiService;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListClubs extends ListRecords
{
    protected static string $resource = ClubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Action::make('updateClubs')
                ->action(function (array $data): void {

                    $result = (new OrisApiService())->updateClubs();

                    if ($result->getStatus() === 'OK') {

                        $newClubs = count($result->getNewItems() ?? []);
                        $updateClubs = count($result->getUpdatedItems() ?? []);

                        Notification::make()
                            ->title('Aktualizace klubů')
                            ->body('Nově přidáno klubů: '.$newClubs.' | Aktualizováno klubů: '.$updateClubs)
                            ->success()
                            ->seconds(8)
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Aktualizace klubů')
                            ->body('Něco se nepovedlo, Můžeš vyzkoušet akci zopakovat nebo kontaktuj admina s popisem chyby, děkujeme.')
                            ->danger()
                            ->send();
                    }
                })

                ->color('gray')
                ->label('Aktualizovat')
                ->icon('heroicon-m-arrow-path')
                ->modalHeading('Aktualizuj Kluby z ORISu')
                ->modalDescription('Načte a uloží/aktualizuje kluby z ORISu.')
                ->modalSubmitActionLabel('Aktualizovat')
                ->form([
                ]),
        ];
    }
}
