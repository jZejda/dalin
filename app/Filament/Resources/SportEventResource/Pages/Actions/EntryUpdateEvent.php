<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages\Actions;

use App\Models\SportEvent;
use App\Services\OrisApiService;
use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action as ModalAction;

class EntryUpdateEvent
{
    private SportEvent $sportEvent;

    public function __construct(SportEvent $sportEvent)
    {
        $this->sportEvent = $sportEvent;
    }

    public function showUpdateEventFromOris(): ModalAction
    {
        return ModalAction::make('updateSportEventUpdate')
            ->action(function (): void {
                if (!is_null($this->sportEvent->oris_id)) {
                    $result = (new OrisApiService())->updateEvent($this->sportEvent->oris_id, true);
                } else {
                    $result = false;
                }

                if ($result) {
                    Notification::make()
                        ->title('Aktualizace závodu')
                        ->body('Závod byl úspěšně aktualizován')
                        ->success()
                        ->seconds(8)
                        ->send();
                } else {
                    Notification::make()
                        ->title('Aktualizace závodu')
                        ->body('Něco se nepovedlo, Můžeš vyzkoušet akci zopakovat nebo kontaktuj admina s popisem chyby, děkujeme.')
                        ->danger()
                        ->send();
                }
            })

            ->color('gray')
            ->label('Aktualizovat závod')
            ->disabled($this->disabledButton())
            ->icon('heroicon-m-arrow-path')
            ->modalHeading('Aktualizovat závod z ORISu')
            ->modalDescription('Provede aktualizaci závodu s aktuálními daty v ORISu')
            ->modalSubmitActionLabel('Aktualizovat')
            ->form([
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
        if($lastUpdate != false && Carbon::now()->gt($lastUpdate->addHours(12))) {
            return false;
        } else {
            return true;
        }
    }
}
