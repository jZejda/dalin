<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserRaceProfiles\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use App\Enums\AppRoles;
use App\Filament\Resources\UserRaceProfiles\UserRaceProfileResource;
use App\Services\OrisApiService;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListUserRaceProfiles extends ListRecords
{
    protected static string $resource = UserRaceProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->updateClubOrisId(),
        ];
    }

    protected function updateClubOrisId(): ?Action
    {
        return Action::make('updateClubOrisId')
            ->action(function (array $data): void {

                $result = (new OrisApiService())->updateUserClubId();

                if ($result) {

                    Notification::make()
                        ->title('Atualizace členství závodníků v klubu proběhla v pořádku')
                        ->body('Členství v klubu proběhlo v pořádku')
                        ->success()
                        ->seconds(8)
                        ->send();
                } else {
                    Notification::make()
                        ->title('eeee')
                        ->body('Něco se nepovedlo, Můžeš vyzkoušet akci zopakovat nebo kontaktuj admina s popisem chyby, děkujeme.')
                        ->danger()
                        ->send();
                }
            })

            ->color('gray')
            ->label('Aktualizovat ID členů v ORISu')
            ->icon('heroicon-m-arrow-path')
            ->modalHeading('Aktualizovat ID členů v ORISu')
            ->modalDescription('Provede hromadnou aktualizaci ID členů oproti ORISU, potřebné pro přihlášky na závod.')
            ->modalSubmitActionLabel('Aktualizovat')
            ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin->value]))
            ->schema([
            ]);
    }
}
