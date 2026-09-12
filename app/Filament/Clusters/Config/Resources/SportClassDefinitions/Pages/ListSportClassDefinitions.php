<?php

namespace App\Filament\Clusters\Config\Resources\SportClassDefinitions\Pages;

use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\Select;
use App\Filament\Clusters\Config\Resources\SportClassDefinitions\SportClassDefinitionResource;
use App\Models\SportList;
use App\Services\OrisApiService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSportClassDefinitions extends ListRecords
{
    protected static string $resource = SportClassDefinitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('updateSportClassDefinition')
                ->action(function (array $data): void {
                    // if notifikace na Discord

                    $result = (new OrisApiService())->updateClassDefinitions($data['sportEventId']);

                    if ($result) {
                        Notification::make()
                            ->title(__('sport-class-definition.actions.update.notification_title'))
                            ->body(__('sport-class-definition.actions.update.notification_body_success'))
                            ->success()
                            ->seconds(8)
                            ->send();
                    } else {
                        Notification::make()
                            ->title(__('sport-class-definition.actions.update.notification_title'))
                            ->body(__('sport-class-definition.actions.update.notification_body_error'))
                            ->danger()
                            ->send();
                    }

                })

                ->color('gray')
                ->label(__('sport-class-definition.actions.update.label'))
                ->icon('heroicon-m-arrow-path')
                ->modalHeading(__('sport-class-definition.actions.update.modal_heading'))
                ->modalDescription(__('sport-class-definition.actions.update.modal_description'))
                ->modalSubmitActionLabel(__('sport-class-definition.actions.update.modal_submit_action_label'))
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Select::make('sportEventId')
                                ->label(__('sport-class-definition.actions.update.sport_event'))
                                ->options(SportList::all()->pluck('short_name', 'id'))
                                ->required()
                                ->columnSpan(2)
                                ->searchable(),
                        ]),

                ]),
        ];
    }
}
