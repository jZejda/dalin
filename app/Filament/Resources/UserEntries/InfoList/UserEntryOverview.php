<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserEntries\InfoList;

use Filament\Schemas\Components\Section;
use Filament\Support\Enums\TextSize;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;

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
                        ->label('Název závodu/akce:')
                        ->size(TextSize::Large),
                    TextEntry::make('sportEvent.date')
                        ->label('Datum konání akce:')
                        ->icon('heroicon-m-calendar-days')
                        ->dateTime('d.m.Y')
                        ->size(TextSize::Large),
                    TextEntry::make('sportEvent.place')
                        ->label('Místo:')
                        ->icon('heroicon-m-map-pin')
                        ->size(TextSize::Large),
                ])
                ->columns(),
            Section::make('Závodní profil')
                ->icon('heroicon-m-user')
                ->description('Detail přihlášeného závodníka, plus ostatní přihlašovací údaje.')
                ->schema([
                    TextEntry::make('class_name')
                        ->label('Kategorie:')
                        ->badge()
                        ->color('success')
                        ->size(TextSize::Large),
                    TextEntry::make('userRaceProfile.UserRaceFullName')
                        ->label('Závodník:')
                        ->size(TextSize::Large),
                    TextEntry::make('note')
                        ->label('Poznámka:')
                        ->placeholder('- nebyla vyplněna -')
                        ->size(TextSize::Large),
                    TextEntry::make('club_note')
                        ->icon('heroicon-m-chevron-right')
                        ->label('Klubová poznámka:')
                        ->size(TextSize::Large)
                        ->placeholder('- nebyla vyplněna -'),
                    TextEntry::make('requested_start')
                        ->label('Start v:')
                        ->size(TextSize::Large)
                        ->placeholder('- nebyl požadován -'),
                    IconEntry::make('rent_si')
                        ->label('Půjčit čip:')
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
                        ->label('Etapy:')
                        ->formatStateUsing(fn (string $state): string => str_replace('stage', 'E', $state))
                        ->placeholder('- jednoetapový závod -'),
                ])
                ->columns(),
        ];
    }
}
