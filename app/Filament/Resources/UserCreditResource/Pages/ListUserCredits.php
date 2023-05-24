<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserCreditResource\Pages;

use App\Filament\Resources\UserCreditResource;
use App\Models\SportEvent;
use App\Services\OrisApiService;
use Filament\Pages\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Grid;
use Illuminate\Contracts\View\View;

class ListUserCredits extends ListRecords
{
    protected static string $resource = UserCreditResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            $this->getEventOrisBalance(),
        ];
    }

    public array $data_list= [
        'calc_columns' => [
            'amount',
        ],
    ];

    protected function getTableContentFooter(): ?View
    {
        return view('filament.resources.user-credit-resource.tables.footer', $this->data_list);
    }

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
            ->color('secondary')
            ->label('Načti vyúčtování z ORISu')
            ->icon('heroicon-s-save')
            ->modalHeading('Stáhne vyúčtování z ORIS závodu')
            ->modalSubheading('Vyber závod a našti vyúčtování. Akti můžeš provést opakovaně.')
            ->modalButton('Stáhnout vyúčtování')
            ->form([
                Grid::make(1)
                    ->schema([
                        Select::make('sportEventId')
                            ->label('Závod/událost')
                            ->options(SportEvent::all()->sortBy('date')->pluck('sport_event_last_cost_calculate', 'id'))
                            ->required()
                            ->columnSpan(2)
                            ->searchable(),
                    ]),

            ]);
    }

    public function filterFromDay(string $from, string $until): void
    {
        $this->tableFilters['date_range']['created_from'] = $from;
        $this->tableFilters['date_range']['created_until'] = $until;

        $this->getTableFilters();
    }
}
