<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\Action;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Services\OrisApiService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewSportEvent extends ViewRecord
{
    protected static string $resource = SportEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            $this->showUpdateEventFromOris(),
        ];
    }

    protected function showUpdateEventFromOris(): ?Action
    {
        return Action::make('updateSportEvent')
            ->action(function (array $data): void {

                $result = (new OrisApiService())->updateEvent($this->data['oris_id']);

                if ($result) {
                    Notification::make()
                        ->title(__('sport-event.actions.update_event.notification_title'))
                        ->body(__('sport-event.actions.update_event.notification_body_success'))
                        ->success()
                        ->seconds(8)
                        ->send();
                } else {
                    Notification::make()
                        ->title(__('sport-event.actions.update_event.notification_title'))
                        ->body(__('sport-event.actions.update_event.notification_body_error'))
                        ->danger()
                        ->send();
                }
            })

            ->color('gray')
            ->label(__('sport-event.actions.update_event.label'))
            ->disabled(! $this->data['use_oris_for_entries'])
            ->icon('heroicon-m-arrow-path')
            ->modalHeading(__('sport-event.actions.update_event.modal_heading'))
            ->modalDescription(__('sport-event.actions.update_event.modal_description'))
            ->modalSubmitActionLabel(__('sport-event.actions.update_event.modal_submit'))
            ->visible(auth()->user()->hasRole(['super_admin', 'event_master']))
            ->schema([
            ]);
    }
}
