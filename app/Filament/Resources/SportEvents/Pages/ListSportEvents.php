<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages;

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
            // Použít uživatelské filtry
            foreach ($userFilters as $filterId => $filter) {
                $tabs[$filterId] = Tab::make()
                    ->label($this->generateFilterName($filter))
                    ->badgeColor('success')
                    ->icon($this->mapIconToHeroicon($filter['icon'] ?? null))
                    ->modifyQueryUsing($this->buildQueryModifier($filter));
            }
        } else {
            // Použít výchozí záložky
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
     * Načte uživatelsky definované filtry z user_settings
     *
     * @return array<string, array<string, mixed>>
     */
    private function getUserFilters(): array
    {
        $user = Auth::user();
        if (!$user) {
            return [];
        }

        $filtersSetting = UserSetting::where('user_id', '=', $user->id)
            ->where('type', '=', 'filters')
            ->first();

        if (!$filtersSetting || !isset($filtersSetting->options['filters'])) {
            return [];
        }

        return $filtersSetting->options['filters'] ?? [];
    }

    /**
     * Mapuje custom ikonu na heroicon ikonu
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
     * Vygeneruje název filtru z parametrů, pokud není definován name
     *
     * @param array<string, mixed> $filter
     * @return string
     */
    private function generateFilterName(array $filter): string
    {
        // Pokud má filtr definovaný name, použij ho
        if (isset($filter['name']) && !empty($filter['name'])) {
            return $filter['name'];
        }

        $parts = [];

        // Sport
        if (isset($filter['sport_list']) && !empty($filter['sport_list'])) {
            $sportId = (int) $filter['sport_list'];
            $sport = SportList::find($sportId);
            if ($sport) {
                $parts[] = $sport->short_name;
            }
        }

        // Typ události
        if (isset($filter['sport_event_type']) && !empty($filter['sport_event_type'])) {
            $eventType = $filter['sport_event_type'];
            $eventTypeLabel = match ($eventType) {
                'race' => 'Závody',
                'training' => 'Trénink',
                'trainingCamp' => 'Soustředění',
                'other' => 'Ostatní',
                default => $eventType,
            };
            $parts[] = $eventTypeLabel;
        }

        // Počet dní
        if (isset($filter['days_from_today']) && !empty($filter['days_from_today'])) {
            $days = (int) $filter['days_from_today'];
            $parts[] = "{$days} dní";
        }

        return !empty($parts) ? implode(', ', $parts) : 'Filtr';
    }

    /**
     * Vytvoří query modifikátor pro filtr na základě parametrů
     *
     * @param array<string, mixed> $filter
     * @return Closure
     */
    private function buildQueryModifier(array $filter): Closure
    {
        return function (Builder $query) use ($filter): Builder {
            // Filtrování podle sport_id
            if (isset($filter['sport_list']) && !empty($filter['sport_list'])) {
                $sportId = (int) $filter['sport_list'];
                $query->where('sport_id', '=', $sportId);
            }

            // Filtrování podle event_type
            if (isset($filter['sport_event_type']) && !empty($filter['sport_event_type'])) {
                $eventType = $filter['sport_event_type'];
                try {
                    $eventTypeEnum = SportEventType::from($eventType);
                    $query->where('event_type', '=', $eventTypeEnum);
                } catch (\ValueError $e) {
                    // Neplatný event_type, ignorujeme
                }
            }

            // Filtrování podle data (od dneška + days_from_today)
            if (isset($filter['days_from_today']) && !empty($filter['days_from_today'])) {
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
