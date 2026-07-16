<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages;

use App\Enums\AppHeroIcons;
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
use App\Models\UserSetting;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ListSportEvents extends ListRecords
{
    protected static string $resource = SportEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            (new AddOrisEventModal())->getAction(),
            $this->getNotifiAction()->tooltip(__('sport-event.actions.send_notification.tooltip')),
        ];
    }

    //    public function table(Table $table): Table
    //    {
    //        $table = parent::table($table);
    //
    //        // If user has custom filters, remove default table filters
    //        if (EmptyType::arrayNotEmpty($this->getUserFilters())) {
    //            $table->filters([]);
    //        }
    //
    //        return $table;
    //    }

    public function getTabs(): array
    {
        $tabs = [];
        $userFilters = $this->getUserFilters();

        if (EmptyType::arrayNotEmpty($userFilters)) {
            foreach ($userFilters as $filterId => $filter) {
                $tabs[$filterId] = Tab::make()
                    ->label($filter['name'])
                    ->badgeColor('success')
                    ->icon(AppHeroIcons::tryFrom($filter['icon'])->getIcon())
                    ->modifyQueryUsing($this->buildQueryModifier($filter));
            }
        } else {
            // Default filters
            $tabs['race'] = Tab::make()
                ->label(__('sport-event.pages.list.tab_races'))
                ->badgeColor('success')
                ->icon('heroicon-m-flag')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('event_type', '=', SportEventType::Race));
            $tabs['traing'] = Tab::make()
                ->label(__('sport-event.pages.list.tab_training'))
                ->badgeColor('success')
                ->icon('heroicon-m-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('event_type', '=', SportEventType::Training));
            $tabs['trainingCamp'] = Tab::make()
                ->label(__('sport-event.pages.list.tab_training_camp'))
                ->badgeColor('success')
                ->icon('heroicon-m-calendar-days')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('event_type', '=', SportEventType::TrainingCamp));
            $tabs['other'] = Tab::make()
                ->label(__('sport-event.pages.list.tab_other'))
                ->badgeColor('success')
                ->icon('heroicon-m-exclamation-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query
                    ->where('event_type', '=', SportEventType::Other));
        }

        $tabs['all'] = Tab::make()
            ->label(__('sport-event.pages.list.tab_all'));

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

        $filters = $filtersSetting->options['event_filters'];
        if (!is_array($filters)) {
            return [];
        }

        if (!empty($filters)) {
            usort($filters, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));
        }

        // Key filters by UUID
        $keyedFilters = [];
        foreach ($filters as $filter) {
            $key = $filter['id'] ?? (string) Str::uuid();
            $keyedFilters[$key] = $filter;
        }
        return $keyedFilters;
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
                        ->title(__('sport-event.actions.send_notification.notification_title'))
                        ->body(__('sport-event.actions.send_notification.notification_body'))
                        ->success()
                        ->seconds(8)
                        ->send();
                })
                ->color('gray')
                ->label(__('sport-event.actions.send_notification.label'))
                ->icon('heroicon-s-paper-airplane')
                ->modalHeading(__('sport-event.actions.send_notification.modal_heading'))
                ->modalDescription(__('sport-event.actions.send_notification.modal_description'))
                ->modalSubmitActionLabel(__('sport-event.actions.send_notification.modal_submit'))
                ->visible(Auth::user()?->hasRole([AppRoles::SuperAdmin->value, AppRoles::EventMaster->value]) ?? false)
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('sportEventId')
                                ->label(__('sport-event.actions.send_notification.event'))
                                ->options(SportEvent::all()->pluck('sport_event_oris_title', 'id'))
                                ->required()
                                ->columnSpan(2)
                                ->searchable(),
                            Select::make('notificationType')
                                ->label(__('sport-event.actions.send_notification.notification_type'))
                                ->options([
                                    DiscordWebhookHelper::CONTENT_STATUS_NEW => __('sport-event.actions.send_notification.notification_type_new'),
                                    DiscordWebhookHelper::CONTENT_STATUS_UPDATE => __('sport-event.actions.send_notification.notification_type_update'),
                                ])
                                ->default(DiscordWebhookHelper::CONTENT_STATUS_NEW)
                                ->required(),
                            Select::make('chanelId')
                                ->label(__('sport-event.actions.send_notification.channel'))
                                ->options([
                                    1 => __('sport-event.actions.send_notification.channel_discord'),
                                    2 => __('sport-event.actions.send_notification.channel_email'),
                                ])
                                ->default(1)
                                ->required(),
                        ]),

                ]);
    }

}
