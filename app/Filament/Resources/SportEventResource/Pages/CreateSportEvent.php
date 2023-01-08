<?php

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Filament\Resources\SportEventResource;
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
}
