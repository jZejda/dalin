<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions;

use Filament\Actions\Action;
use App\Models\SportEvent;
use App\Services\OrisApiService;
use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class EntryUpdateEvent
{
    private SportEvent $sportEvent;

    public function __construct(SportEvent $sportEvent)
    {
        $this->sportEvent = $sportEvent;
    }

    public function showUpdateEventFromOris(): Action
    {
        return Action::make('updateSportEventUpdate')
            ->action(function (): void {
                if (!is_null($this->sportEvent->oris_id)) {
                    $result = (new OrisApiService())->updateEvent($this->sportEvent->oris_id, true);
                } else {
                    $result = false;
                }

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
            ->disabled($this->disabledButton())
            ->icon('heroicon-m-arrow-path')
            ->modalHeading(__('sport-event.actions.update_event.modal_heading'))
            ->modalDescription(__('sport-event.actions.update_event.modal_description'))
            ->modalSubmitActionLabel(__('sport-event.actions.update_event.modal_submit'))
            ->schema([
            ]);
    }

    private function disabledButton(): bool
    {
        if (is_null($this->sportEvent->last_update)) {
            return false;
        }

        if (is_null($this->sportEvent->oris_id)) {
            return true;
        }

        $lastUpdate = Carbon::createFromFormat(AppHelper::MYSQL_DATE_TIME, $this->sportEvent->last_update);
        if ($lastUpdate != false && Carbon::now()->gt($lastUpdate->addHours(12))) {
            return false;
        } else {
            return true;
        }
    }
}
