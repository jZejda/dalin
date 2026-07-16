<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages;

use App\Filament\Resources\SportEvents\SportEventResource;
use App\Services\OrisApiService;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSportEvent extends EditRecord
{
    protected static string $resource = SportEventResource::class;

    /**
     * @return array{Action|null}
     */
    protected function getHeaderActions(): array
    {
        return [
            $this->showUpdateEventFromOris(),
            DeleteAction::make(),
        ];
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 1;
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // EventEditMap::class,
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
            ->disabled((!$this->data['use_oris_for_entries'] && is_null($this->data['oris_id'])))
            ->icon('heroicon-m-arrow-path')
            ->modalHeading(__('sport-event.actions.update_event.modal_heading'))
            ->modalDescription(__('sport-event.actions.update_event.modal_description'))
            ->modalSubmitActionLabel(__('sport-event.actions.update_event.modal_submit'))
            ->visible(auth()->user()->hasRole(['super_admin', 'event_master']))
            ->schema([
            ]);
    }
}
