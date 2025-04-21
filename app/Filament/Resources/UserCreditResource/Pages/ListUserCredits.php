<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserCreditResource\Pages;

use App\Enums\AppRoles;
use App\Filament\Pages\Actions\ExportUserRaceProfileData;
use App\Filament\Resources\UserCreditResource;
use App\Models\SportEvent;
use App\Services\OrisApiService;
use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;
use Filament\Actions\ActionGroup;
use Filament\Pages\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Grid;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ListUserCredits extends ListRecords
{
    protected static string $resource = UserCreditResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
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
                (new UserCreditResource\Actions\AddUserTransportBillingModal())->getAction(),
            ]
        )->button()
            ->icon('heroicon-o-plus-circle')
            ->color('gray')
            ->label('Nový záznam');
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
            ->label('Načti vyúčtování z ORISu')
            ->icon('heroicon-o-arrow-down-tray')
            ->modalHeading('Stáhne vyúčtování z ORIS závodu')
            ->modalDescription('Vyber závod a načti vyúčtování. Akci můžeš provést opakovaně.')
            ->modalSubmitActionLabel('Stáhnout vyúčtování')
            ->modalIcon('heroicon-o-arrow-down-tray')
            ->modalIconColor('success')
            ->form([
                Grid::make(1)
                    ->schema([
                        Select::make('sportEventId')
                            ->label('Závod/událost')
                            ->options(
                                SportEvent::all()
                                    ->sortBy('date')
                                    ->whereNotNull('oris_id')
                                    ->where('created_at', '>', Carbon::now()->subMonths(12)->format(AppHelper::MYSQL_DATE_TIME))
                                    ->sortByDesc('date')
                                    ->pluck('sport_event_last_cost_calculate', 'id')
                            )
                            ->required()
                            ->columnSpan(2),
                    ]),

            ]);
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
