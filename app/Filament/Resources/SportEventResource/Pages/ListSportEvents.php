<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Enums\AppRoles;
use App\Enums\SportEventType;
use Closure;
use Filament\Forms;
use App\Filament\Resources\SportEventResource;
use App\Filament\Resources\SportEventResource\Pages\Actions\AddOrisEventModal;
use App\Http\Controllers\Discord\DiscordWebhookHelper;
use App\Http\Controllers\Discord\RaceEventAddedNotification;
use App\Models\SportEvent;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ListSportEvents extends ListRecords
{
    protected static string $resource = SportEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            (new AddOrisEventModal())->getAction(),
            $this->getNotifiAction()->tooltip('Umožní poslat ručně notifikaci na vybraný kanál.'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->label('Vše'),
            'race' => Tab::make()
                ->label('Závody')
                ->badgeColor('success')
                ->icon('heroicon-m-flag')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event_type', '=', SportEventType::Race)),
            'traing' => Tab::make()
                ->label('Trénink')
                ->badgeColor('success')
                ->icon('heroicon-m-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event_type', '=', SportEventType::Training)),
            'trainingCamp' => Tab::make()
                ->label('Soustředění')
                ->badgeColor('success')
                ->icon('heroicon-m-calendar-days')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event_type', '=', SportEventType::TrainingCamp)),
            'other' => Tab::make()
                ->label('Ostatní')
                ->badgeColor('success')
                ->icon('heroicon-m-exclamation-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event_type', '=', SportEventType::Other)),
        ];
    }

    protected function getTableRecordUrlUsing(): ?Closure
    {
        return fn (Model $record): string => route('filament.admin.resources.sport-events.entry', ['record' => $record]);
    }

    public function openSettingsModal(): void
    {
        $this->dispatch('open-settings-modal');
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
                ->color('gray')
                ->label('Pošli notifikaci')
                ->icon('heroicon-s-paper-airplane')
                ->modalHeading('Pošli notifikaci k závodu/akci')
                ->modalDescription('Notifikace je možná poslat do různých kanálů na objekty, jakékoliv objekty v listu')
                ->modalSubmitActionLabel('Ano poslat notifikaci')
                ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin->value, AppRoles::EventMaster->value]))
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
