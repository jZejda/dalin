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
                            ->options(SportEvent::all()->pluck('sport_event_oris_title', 'id')->sortByDesc('date'))
                            ->required()
                            ->columnSpan(2)
                            ->searchable(),
                    ]),

            ]);
    }
}
