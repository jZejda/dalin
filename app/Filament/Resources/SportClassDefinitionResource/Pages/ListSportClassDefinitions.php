<?php

namespace App\Filament\Resources\SportClassDefinitionResource\Pages;

use App\Filament\Resources\SportClassDefinitionResource;
use App\Models\SportList;
use App\Services\OrisApiService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSportClassDefinitions extends ListRecords
{
    protected static string $resource = SportClassDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('updateSportClassDefinition')
                ->action(function (array $data): void {
                    // if notifikace na Discord

                    $result = (new OrisApiService())->updateClassDefinitions($data['sportEventId']);

                    if ($result) {
                        Notification::make()
                            ->title('Aktualizace definic kategorií')
                            ->body('AKtualizace definic kategorií z ORISu proběhla v pořádku.')
                            ->success()
                            ->seconds(8)
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Aktualizace definic kategorií')
                            ->body('Něco se nepovedlo, Můžeš vyzkoušet akci zopakovat nebo kontaktuj admina s popisem chyby, děkujeme.')
                            ->danger()
                            ->send();
                    }

                })

                ->color('gray')
                ->label('Aktualizovat')
                ->icon('heroicon-m-arrow-path')
                ->modalHeading('Aktualizuj definici kategorií z ORISu')
                ->modalDescription('Definice kategorii, podle ORISU. V dialogu zvol pro jaký sport chceš zaktualizovat definice kategorií.')
                ->modalSubmitActionLabel('Aktualizovat')
                ->form([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('sportEventId')
                                ->label('Závod/událost')
                                ->options(SportList::all()->pluck('short_name', 'id'))
                                ->required()
                                ->columnSpan(2)
                                ->searchable(),
                        ]),

                ]),
        ];
    }
}
