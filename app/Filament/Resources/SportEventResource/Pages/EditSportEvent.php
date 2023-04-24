<?php

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Enums\SportEventMarkerType;
use App\Filament\Resources\SportEventResource;
use App\Filament\Resources\SportEventResource\Widgets\EventEditMap;
use App\Models\SportEventMarker;
use App\Services\OrisApiService;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;

class EditSportEvent extends EditRecord
{
    protected static string $resource = SportEventResource::class;

    protected function getActions(): array
    {
        return [
            $this->addEventMarker(),
            $this->showUpdateEventFromOris(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EventEditMap::class,
        ];
    }

    protected function addEventMarker(): ?Action
    {
        return Action::make('addMarkerToEvent')
            ->action(function (array $data): void {

                $newMarker = new SportEventMarker();
                $newMarker->sport_event_id = $this->data['id'];
                $newMarker->label = $data['name'];
                $newMarker->lat = $data['lat'];
                $newMarker->lon = $data['lng'];
                $newMarker->desc = $data['desc'];
                $newMarker->type = $data['event_type'];
                $newMarker->saveOrFail();

                Notification::make()
                    ->title('Přidán bod zájmu')
                    ->body('K události byl přidán bod zájmu.')
                    ->success()
                    ->seconds(8)
                    ->send();
            })


            ->color('secondary')
            ->label('Přidat bod')
            ->icon('heroicon-s-location-marker')
            ->modalHeading('Přidání bodu zájmu')
            ->modalSubheading('Přidá k události další bod zájmu. Zobrazí se na mapě detailu závodu.')
            ->modalButton('Přidat')
            ->visible(auth()->user()->hasRole(['super_admin', 'event_master']))
            ->form([
                TextInput::make('name')
                        ->label('Name')
                        ->required(),
                    TextInput::make('lat')
                        ->label('Latitude')
                        ->numeric()
                        ->required(),
                    TextInput::make('lng')
                        ->label('Longitude')
                        ->numeric()
                        ->required(),
                    TextInput::make('desc')
                        ->label('Popis bodu')
                        ->required(),
                    Select::make('event_type')
                        ->label('Typ bodu')
                        ->options(SportEventMarkerType::enumArray())
                        ->default(SportEventMarkerType::StageStart->value),
        ]);
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
