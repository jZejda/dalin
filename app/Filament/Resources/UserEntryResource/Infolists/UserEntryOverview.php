<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserEntryResource\Infolists;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;

class UserEntryOverview
{
    public static function getOverview(): array
    {
        return [
            Section::make('Přihláška')
                ->icon('heroicon-m-user')
                ->description('Detail přihlášky k závodu')
                ->schema([
                    TextEntry::make('sportEvent.name')
                        ->label('Název závodu/akce')
                        ->size(TextEntrySize::Large),
                    TextEntry::make('sportEvent.date')
                        ->label('Datum konání akce')
                        ->dateTime('d. m. Y'),
                    TextEntry::make('sportEvent.place')
                        ->label('Místo')
                        ->hint('Documentation? What documentation?!'),
                ])
                ->columns(),
            Section::make('Závodní profil')
                ->icon('heroicon-m-user')
                ->description('Detail přihlášeného závodníka.')
                ->schema([
                    TextEntry::make('class_name')
                        ->label('Kategorie')
                        ->size(TextEntrySize::Large),
                    TextEntry::make('userRaceProfile.UserRaceFullName')
                        ->label('Popis'),
                ])
                ->columns(),
        ];
    }
}
