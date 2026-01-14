<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages;

use App\Shared\Helpers\EmptyType;
use Filament\Actions\CreateAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Actions\Action;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use App\Enums\AppRoles;
use App\Enums\SportEventType;
use Closure;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Filament\Resources\SportEvents\Pages\Actions\AddOrisEventModal;
use App\Http\Controllers\Discord\DiscordWebhookHelper;
use App\Http\Controllers\Discord\RaceEventAddedNotification;
use App\Models\SportEvent;
use App\Models\SportList;
use App\Models\UserSetting;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ListSportEvents extends ListRecords
{
    protected static string $resource = SportEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            (new AddOrisEventModal())->getAction(),
            $this->getNotifiAction()->tooltip('Umožní poslat ručně notifikaci na vybraný kanál.'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make()
                ->label('Vše'),
        ];

        $userFilters = $this->getUserFilters();

        if (!empty($userFilters)) {
            foreach ($userFilters as $filterId => $filter) {
                $tabs[$filterId] = Tab::make()
                    ->label($this->generateFilterName($filter))
                    ->badgeColor('success')
                    ->icon($this->mapIconToHeroicon($filter['icon'] ?? null))
                    ->modifyQueryUsing($this->buildQueryModifier($filter));
            }
        } else {
            // Default filters
            $tabs['race'] = Tab::make()
                ->label('Závody')
                ->badgeColor('success')
                ->icon('heroicon-m-flag')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event_type', '=', SportEventType::Race));
            $tabs['traing'] = Tab::make()
                ->label('Trénink')
                ->badgeColor('success')
                ->icon('heroicon-m-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event_type', '=', SportEventType::Training));
            $tabs['trainingCamp'] = Tab::make()
                ->label('Soustředění')
                ->badgeColor('success')
                ->icon('heroicon-m-calendar-days')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event_type', '=', SportEventType::TrainingCamp));
            $tabs['other'] = Tab::make()
                ->label('Ostatní')
                ->badgeColor('success')
                ->icon('heroicon-m-exclamation-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('event_type', '=', SportEventType::Other));
        }

        return $tabs;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function getUserFilters(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $filtersSetting = UserSetting::where('user_id', '=', $user->id)
            ->where('type', '=', UserSetting::USER_EVENT_FILTERS_NAME)
            ->first();

        if (!$filtersSetting || !isset($filtersSetting->options['event_filters'])) {
            return [];
        }

        return $filtersSetting->options['event_filters'] ?? [];
    }

    /**
     * Custom icons map onto heroicons
     *
     * @param string|null $icon
     * @return string
     */
    private function mapIconToHeroicon(?string $icon): string
    {
        return match ($icon) {
            'obRaceStages' => 'heroicon-m-flag',
            'obRaceDot' => 'heroicon-m-flag',
            'obRaceSimple' => 'heroicon-m-flag',
            default => 'heroicon-m-flag',
        };
    }

    /**
     * @param array<string, mixed> $filter
     * @return string
     */
    private function generateFilterName(array $filter): string
    {
        if (EmptyType::arrayNotEmpty($filter['name'])) {
            return $filter['name'];
        }

        $parts = [];

        if (EmptyType::arrayNotEmpty($filter['sport_list'])) {
            $sportIds = array_map('intval', $filter['sport_list']);

            $sportNames = SportList::whereIn('id', $sportIds)->pluck('short_name')->toArray();
            if (!empty($sportNames)) {
                $parts[] = implode(', ', $sportNames);
            }
        }

        if (EmptyType::arrayNotEmpty($filter['sport_event_type'])) {
            $eventTypes =  $filter['sport_event_type'];

            $eventTypeLabels = array_map(fn ($eventType) => match ($eventType) {
                'race' => 'Závody',
                'training' => 'Trénink',
                'trainingCamp' => 'Soustředění',
                'other' => 'Ostatní',
                default => $eventType,
            }, $eventTypes);
            $parts[] = implode(', ', $eventTypeLabels);
        }

        // Počet dní
        if (EmptyType::arrayNotEmpty($filter['days_from_today'])) {
            $days = (int) $filter['days_from_today'];
            $parts[] = "{$days} dní";
        }

        return !empty($parts) ? implode(', ', $parts) : 'Filtr';
    }

    /**
     * @param array<string, mixed> $filter
     * @return Closure
     */
    private function buildQueryModifier(array $filter): Closure
    {
        return function (Builder $query) use ($filter): Builder {
            if (EmptyType::arrayNotEmpty($filter['sport_list'])) {
                $sportIds = array_map('intval', $filter['sport_list']);
                $query->whereIn('sport_id', $sportIds);
            }

            // Filtrování podle event_type (může být pole nebo jednotlivá hodnota)
            if (EmptyType::arrayNotEmpty($filter['sport_event_type'])) {
                $eventTypes = $filter['sport_event_type'];

                $eventTypeEnums = [];
                foreach ($eventTypes as $eventType) {
                    try {
                        $eventTypeEnums[] = SportEventType::from($eventType);
                    } catch (\ValueError $e) {
                        // Neplatný event_type, ignorujeme
                    }
                }

                if (!empty($eventTypeEnums)) {
                    $query->whereIn('event_type', $eventTypeEnums);
                }
            }

            if (isset($filter['days_from_today']) && EmptyType::stringNotEmpty($filter['days_from_today'])) {
                $days = (int) $filter['days_from_today'];
                $fromDate = Carbon::today()->addDays($days);
                $query->whereDate('date', '>=', $fromDate);
            }

            return $query;
        };
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
                ->visible(Auth::user()?->hasRole([AppRoles::SuperAdmin->value, AppRoles::EventMaster->value]) ?? false)
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('sportEventId')
                                ->label('Závod/událost')
                                ->options(SportEvent::all()->pluck('sport_event_oris_title', 'id'))
                                ->required()
                                ->columnSpan(2)
                                ->searchable(),
                            Select::make('notificationType')
                                ->label('Typ upozornění')
                                ->options([
                                    DiscordWebhookHelper::CONTENT_STATUS_NEW => 'Nová událost',
                                    DiscordWebhookHelper::CONTENT_STATUS_UPDATE => 'Upravená událost'
                                ])
                                ->default(DiscordWebhookHelper::CONTENT_STATUS_NEW)
                                ->required(),
                            Select::make('chanelId')
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
