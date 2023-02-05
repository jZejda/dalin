<?php

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Filament\Resources\SportEventResource;
use App\Models\SportEvent;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreateSportEvent extends CreateRecord
{
    protected static string $resource = SportEventResource::class;


    protected function getActions(): array
    {
        return [
            Action::make('settings')->action('openSettingsModal'),
        ];
    }

    public function openSettingsModal(): void
    {
        $this->dispatchBrowserEvent('open-settings-modal');
    }

    protected function afterCreate(): void
    {
        /** @var User $recipient */
        $recipient = auth()->user();

        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        Notification::make()
            ->title('Vytvořen nový závod')
            ->body('Uživatel: ' . $recipient->name . ' | Název: ' . $sportEvent->name)
            ->sendToDatabase($recipient);
    }
}
