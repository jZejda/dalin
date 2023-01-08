<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\Pages;

use App\Filament\Resources\SportEventResource;
use App\Http\Controllers\Discord\RaceEventAddedNotification;
use Filament\Facades\Filament;
use Filament\Pages\Actions\Action;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSportEvent extends ViewRecord
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
                ->action('sendDiscordNotification'),
        ];
    }

    public function sendDiscordNotification(): void
    {

        (new RaceEventAddedNotification($this->getRecord()))->notification();
        Filament::notify('danger', 'Nepodařilo se načíst data.');
    }
}
