<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Config\Resources\Clubs\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use App\Filament\Clusters\Config\Resources\Clubs\ClubResource;
use App\Services\OrisApiService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListClubs extends ListRecords
{
    protected static string $resource = ClubResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            Action::make('updateClubs')
                ->action(function (array $data): void {

                    $result = (new OrisApiService())->updateClubs();

                    if ($result->getStatus() === 'OK') {

                        $newClubs = count($result->getNewItems() ?? []);
                        $updateClubs = count($result->getUpdatedItems() ?? []);

                        Notification::make()
                            ->title(__('club.actions.update.notification_title'))
                            ->body(__('club.actions.update.notification_body_success', ['new' => $newClubs, 'updated' => $updateClubs]))
                            ->success()
                            ->seconds(8)
                            ->send();
                    } else {
                        Notification::make()
                            ->title(__('club.actions.update.notification_title'))
                            ->body(__('club.actions.update.notification_body_error'))
                            ->danger()
                            ->send();
                    }
                })

                ->color('gray')
                ->label(__('club.actions.update.label'))
                ->icon('heroicon-m-arrow-path')
                ->modalHeading(__('club.actions.update.modal_heading'))
                ->modalDescription(__('club.actions.update.modal_description'))
                ->modalSubmitActionLabel(__('club.actions.update.modal_submit_action_label'))
                ->schema([
                ]),
        ];
    }
}
