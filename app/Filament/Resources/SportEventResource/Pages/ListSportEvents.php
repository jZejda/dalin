<?php

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Filament\Resources\SportEventResource;
use Filament\Pages\Actions;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListSportEvents extends ListRecords
{
    protected static string $resource = SportEventResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Action::make('settings')
                ->label('pocli discord')
                ->color('secondary')
                ->icon('heroicon-s-cog')
                ->action('openSettingsModal'),
        ];
    }

    public function openSettingsModal(): void
    {
        dd('fsfsfs');





       // $this->dispatchBrowserEvent('open-settings-modal');
    }
}
