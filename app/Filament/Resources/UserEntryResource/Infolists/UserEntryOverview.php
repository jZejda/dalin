<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserEntryResource\Infolists;

use Filament\Infolists\Components\IconEntry;
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
                ->description('Detail přihlášky na zvolený závod nebo akci.')
                ->schema([
                    TextEntry::make('sportEvent.name')
                        ->label('Název závodu/akce')
                        ->size(TextEntrySize::Large),
                    TextEntry::make('sportEvent.date')
                        ->label('Datum konání akce')
                        ->icon('heroicon-m-calendar-days')
                        ->dateTime('d.m.Y')
                        ->size(TextEntrySize::Large),
                    TextEntry::make('sportEvent.place')
                        ->label('Místo')
                        ->icon('heroicon-m-map-pin')
                        ->size(TextEntrySize::Large),
                ])
                ->columns(),
            Section::make('Závodní profil')
                ->icon('heroicon-m-user')
                ->description('Detail přihlášeného závodníka, plus ostatní přihlašovací údaje.')
                ->schema([
                    TextEntry::make('class_name')
                        ->label('Kategorie')
                        ->badge()
                        ->color('success')
                        ->size(TextEntrySize::Large),
                    TextEntry::make('userRaceProfile.UserRaceFullName')
                        ->label('Popis')
                        ->size(TextEntrySize::Large),
                    TextEntry::make('note')
                        ->label('Poznámka')
                        ->placeholder('- nebyla vyplněna -')
                        ->size(TextEntrySize::Large),
                    TextEntry::make('club_note')
                        ->icon('heroicon-m-chevron-right')
                        ->label('Klubová poznámka')
                        ->size(TextEntrySize::Large)
                        ->placeholder('- nebyla vyplněna -'),
                    TextEntry::make('requested_start')
                        ->label('Start v')
                        ->size(TextEntrySize::Large)
                        ->placeholder('- nebyl požadován -'),
                    IconEntry::make('rent_si')
                        ->label('SI')
                        ->icon(fn (int $state): string => match ($state) {
                            0 => 'heroicon-m-no-symbol',
                            1 => 'heroicon-o-check',
                            default => 'heroicon-o-exclamation-circle',
                        })
                        ->color(fn (int $state): string => match ($state) {
                            0 => 'gray',
                            1 => 'success',
                            default => 'gray',
                        }),
                    TextEntry::make('entry_stages')
                        ->badge()
                        ->separator(',')
                        ->label('Etapy')
                        ->formatStateUsing(fn (string $state): string => str_replace('stage', 'E', $state))
                        ->placeholder('- jednoetapový závod -'),
                ])
                ->columns(),
        ];
    }
}
