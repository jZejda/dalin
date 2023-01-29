<?php

namespace App\Filament\Resources\SportClassDefinitionResource\Pages;

use App\Filament\Resources\SportClassDefinitionResource;
use App\Http\Controllers\Discord\DiscordWebhookHelper;
use App\Http\Controllers\Discord\RaceEventAddedNotification;
use App\Models\SportEvent;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;

class ListSportClassDefinitions extends ListRecords
{
    protected static string $resource = SportClassDefinitionResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('updateAuthor')
                ->action(function (array $data): void {
                    // if notifikace na Discord
                    /** @var SportEvent $sportEvent */
                    $sportEvent = SportEvent::query()->where('id','=', $data['sportEventId'])->first();
                    (new RaceEventAddedNotification($sportEvent, $data['notificationType']))->sendNotification();
                    Notification::make()
                        ->title('Aktualizace definic kategorií')
                        ->body('AKtualizace definic kategorií z ORISu proběhla v pořádku.')
                        ->success()
                        ->seconds(8)
                        ->send();
                })
                ->color('secondary')
                ->label('Pošli notifikaci')
                ->icon('heroicon-s-refresh')
                ->modalHeading('Zaktualizuj definici kategorií z ORISu')
                ->modalSubheading('fdsfsfsdfsdfsdfsdfsfsfsdfs')
                ->modalButton('Aktualizovat')
                ->form([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('sportEventId')
                                ->label('Závod/událost')
                                ->options(SportEvent::all()->pluck('sport_event_oris_title', 'id'))
                                ->required()
                                ->columnSpan(2)
                                ->searchable(),
                            Forms\Components\Select::make('notificationType')
                                ->label('Typ upozornění')
                                ->options([
                                    DiscordWebhookHelper::CONTENT_STATUS_NEW => 'Nová událost',
                                    DiscordWebhookHelper::CONTENT_STATUS_UPDATE => 'Upravená událost'
                                ])
                                ->default(DiscordWebhookHelper::CONTENT_STATUS_NEW)
                                ->required(),
                            Forms\Components\Select::make('chanelId')
                                ->label('Kanál')
                                ->options([
                                    1 => 'Discord',
                                    2 => 'E-mail'
                                ])
                                ->default(1)
                                ->required(),
                        ]),

                ])
        ];
    }
}
