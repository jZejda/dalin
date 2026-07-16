<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserCredits\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use Filament\Schemas\Components\Grid;
use App\Enums\AppRoles;
use App\Enums\UserCreditType;
use App\Filament\Resources\UserCredits\Actions\AddUserTransferBillingModal;
use App\Filament\Resources\UserCredits\UserCreditResource;
use App\Models\SportEvent;
use App\Services\OrisApiService;
use App\Shared\Helpers\AppHelper;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class ListUserCredits extends ListRecords
{
    protected static string $resource = UserCreditResource::class;

    /** @var array<int, string> */
    protected array $orisBilledBadges = [];

    protected function getHeaderActions(): array
    {
        return [
            $this->getEventOrisBalance(),
            (Auth::user()?->hasRole([
                AppRoles::EventMaster,
                AppRoles::EventOrganizer,
                AppRoles::BillingSpecialist,
                AppRoles::SuperAdmin,
            ])) ? $this->getGroupActions() : ActionGroup::make([]),
        ];
    }

    protected function getGroupActions(): ActionGroup
    {
        return ActionGroup::make(
            [
                CreateAction::make()
                    ->label(__('user-credit.list.new_billing_label'))
                    ->icon('heroicon-o-banknotes')
                    ->modalWidth(Width::SevenExtraLarge),
                (new AddUserTransferBillingModal())->getAction(
                    AddUserTransferBillingModal::ACTION_ADD_USER_TRANSPORT_BILLING,
                    UserCreditType::TransportBilling
                ),
                (new AddUserTransferBillingModal())->getAction(
                    AddUserTransferBillingModal::ACTION_ADD_USER_TRANSFER_BILLING,
                    UserCreditType::TransferCreditBetweenUsers
                ),
            ]
        )->button()
            ->icon('heroicon-o-plus-circle')
            ->color('gray')
            ->label(__('user-credit.list.new_record_label'));
    }

    public array $data_list = [
        'calc_columns' => [
            'amount',
        ],
    ];

    //    protected function getTableContentFooter(): ?View
    //    {
    //        return view('filament.admin.resources.user-credit-resource.tables.footer', $this->data_list);
    //    }

    protected function getHeaderWidgets(): array
    {
        return UserCreditResource::getWidgets();
    }

    private function getEventOrisBalance(): Action
    {
        return Action::make('eventOrisBalance')
            ->action(function (array $data): void {
                // if notifikace na Discord
                /** @var SportEvent $sportEvent */
                $sportEvent = SportEvent::query()->where('id', '=', $data['sportEventId'])->first();

                (new OrisApiService())->getEventBalance($sportEvent);
            })
            ->color('gray')
            ->label(__('user-credit.actions.oris_balance.label'))
            ->icon('heroicon-o-arrow-down-tray')
            ->modalHeading(__('user-credit.actions.oris_balance.modal_heading'))
            ->modalDescription(__('user-credit.actions.oris_balance.modal_description'))
            ->modalSubmitActionLabel(__('user-credit.actions.oris_balance.modal_submit_action_label'))
            ->modalIcon('heroicon-o-arrow-down-tray')
            ->modalIconColor('success')
            ->schema([
                Grid::make(1)
                    ->schema([
                        Select::make('sportEventId')
                            ->label(__('user-credit.actions.oris_balance.sport_event'))
                            ->searchable()
                            ->allowHtml()
                            ->options(fn (): array => $this->getOrisEventOptions())
                            ->getSearchResultsUsing(fn (string $search): array => $this->getOrisEventOptions($search))
                            ->getOptionLabelUsing(function (mixed $value): ?string {
                                $sportEvent = SportEvent::query()->find($value);

                                return $sportEvent instanceof SportEvent ? $this->formatOrisEventOption($sportEvent) : null;
                            })
                            ->required()
                            ->columnSpan(2),
                    ]),

            ]);
    }

    /**
     * @return array<int, string>
     */
    private function getOrisEventOptions(?string $search = null): array
    {
        return SportEvent::query()
            ->whereNotNull('oris_id')
            ->when($search !== null && $search !== '', function (Builder $query) use ($search): Builder {
                return $query->where(function (Builder $query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('alt_name', 'like', "%{$search}%")
                        ->orWhere('oris_id', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('date')
            ->limit(50)
            ->get()
            ->sortBy('date')
            ->mapWithKeys(fn (SportEvent $sportEvent): array => [
                $sportEvent->id => $this->formatOrisEventOption($sportEvent),
            ])
            ->all();
    }

    private function formatOrisEventOption(SportEvent $sportEvent): string
    {
        $labelParts = array_filter([
            $sportEvent->date?->format(AppHelper::DATE_FORMAT),
            count($sportEvent->organization ?? []) > 0 ? Arr::join($sportEvent->organization, ', ') : null,
            $sportEvent->alt_name !== null ? Str::limit($sportEvent->alt_name, 50) : null,
            Str::limit($sportEvent->name, 50),
            $sportEvent->oris_id !== null ? '(ORIS '.$sportEvent->oris_id.')' : null,
        ]);

        return $this->getOrisBilledBadge($sportEvent->last_calculate_cost !== null)
            .' '
            .e(implode(' | ', $labelParts));
    }

    private function getOrisBilledBadge(bool $isBilled): string
    {
        $key = (int) $isBilled;

        if (! isset($this->orisBilledBadges[$key])) {
            $this->orisBilledBadges[$key] = $isBilled
                ? Blade::render('<x-filament::badge size="sm" color="success">'.__('user-credit.actions.oris_balance.badge_billed').'</x-filament::badge>')
                : Blade::render('<x-filament::badge size="sm" color="warning">'.__('user-credit.actions.oris_balance.badge_pending').'</x-filament::badge>');
        }

        return $this->orisBilledBadges[$key];
    }

    public function filterFromDay(string $from, string $until): void
    {
        $this->tableFilters['date_range']['created_from'] = $from;
        $this->tableFilters['date_range']['created_until'] = $until;

        $this->getTableFilters();
    }

    protected function shouldPersistTableSortInSession(): bool
    {
        return true;
    }

    protected function getDefaultTableSortColumn(): ?string
    {
        return 'created_at';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'desc';
    }
}
