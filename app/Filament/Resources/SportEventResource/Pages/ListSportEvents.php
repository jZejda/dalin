<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Filament\Resources\SportEventResource;
use App\Http\Controllers\Discord\DiscordWebhookHelper;
use App\Http\Controllers\Discord\RaceEventAddedNotification;
use App\Models\SportEvent;
use App\Models\SportList;
use App\Models\SportRegion;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms;

class ListSportEvents extends ListRecords
{
    protected static string $resource = SportEventResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            $this->getOrisEvent(),
            $this->getNotifiAction(),
        ];
    }

    public function openSettingsModal(): void
    {
        $this->dispatchBrowserEvent('open-settings-modal');
    }

    private function getOrisEvent(): Action
    {

        return Action::make('orisEvent')
            ->action(function (array $data): void {
                // if notifikace na Discord
                /** @var SportEvent $sportEvent */
                $sportEvent = SportEvent::query()->where('id', '=', $data['sportEventId'])->first();
                (new RaceEventAddedNotification($sportEvent, $data['notificationType']))->sendNotification();
                Notification::make()
                    ->title('Notifikace odeslána')
                    ->body('Na zvolený kanál jsi zaslal notifikaci ke konkrétnímu závodu')
                    ->success()
                    ->seconds(8)
                    ->send();
            })
            ->color('secondary')
            ->label('Přidej závod z ORISU')
            ->icon('heroicon-s-cog')
            ->modalHeading('Přidej závod z ORISU')
            ->modalSubheading('Přidá do systému zvolený závod s daty které aktuálně poskytuje ORIS')
            ->modalButton('Přidej závod')
            ->form([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\Select::make('sport_id')
                            ->label('Vyber typ sportu')
                            ->options(SportList::all()->pluck('short_name', 'id'))
                            ->required()
                            ->default(1)
                            ->searchable(),
                        Forms\Components\Select::make('oris_all')
                            ->label('Zobrazit i neoficiální závody')
                            ->options([
                                0 => 'Pouze oficiální',
                                1 => 'Všechny'
                            ])
                            ->required()
                            ->default(0),
                        Forms\Components\Select::make('region_id')
                            ->label('Region')
                            ->options(SportRegion::all()->pluck('long_name', 'id'))
                            ->required()
                            ->searchable(),
                        Forms\Components\Select::make('notificationType')
                            ->label('Typ upozornění')
                            ->options([
                                DiscordWebhookHelper::CONTENT_STATUS_NEW => 'Nová událost',
                                DiscordWebhookHelper::CONTENT_STATUS_UPDATE => 'Upravená událost'
                            ])
                            ->default(DiscordWebhookHelper::CONTENT_STATUS_NEW)
                            ->required(),
                    ]),

            ]);


    }


    private function getNotifiAction(): Action
    {
        return Action::make('updateAuthor')
                ->action(function (array $data): void {
                    // if notifikace na Discord
                    /** @var SportEvent $sportEvent */
                    $sportEvent = SportEvent::query()->where('id', '=', $data['sportEventId'])->first();
                    (new RaceEventAddedNotification($sportEvent, $data['notificationType']))->sendNotification();
                    Notification::make()
                        ->title('Notifikace odeslána')
                        ->body('Na zvolený kanál jsi zaslal notifikaci ke konkrétnímu závodu')
                        ->success()
                        ->seconds(8)
                        ->send();
                })
                ->color('secondary')
                ->label('Pošli notifikaci')
                ->icon('heroicon-s-cog')
                ->modalHeading('Pošli notifikaci k závodu/akci')
                ->modalSubheading('Notifikace je možná poslat do různých kanálů na objekty, jakékoliv objekty v listu')
                ->modalButton('Ano poslat notifikaci')
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

                ]);
    }

}
