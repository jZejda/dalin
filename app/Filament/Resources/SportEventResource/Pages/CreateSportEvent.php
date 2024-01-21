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


    protected function getHeaderActions(): array
    {
        return [
            Action::make('settings')->action('openSettingsModal'),
        ];
    }

    public function openSettingsModal(): void
    {
        $this->dispatch('open-settings-modal');
    }

    protected function afterCreate(): void
    {
        /** @var User $recipientOrigin */
        $recipientOrigin = auth()->user();

        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        $recipients = User::whereHas("roles", function ($q) { $q->whereIn('name', ['super_admin', 'event_master']); })->get();

        foreach ($recipients as $recipient) {
            Notification::make()
                ->title('Vytvořen nový závod')
                ->body('Uživatel: ' . $recipientOrigin->name . ' | Název: ' . $sportEvent->name)
                ->sendToDatabase($recipient);
        }
    }
}
