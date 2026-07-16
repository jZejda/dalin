<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserEntries\InfoList;

use App\Shared\Helpers\AppHelper;
use Filament\Schemas\Components\Section;
use Filament\Support\Enums\TextSize;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;

class UserEntryOverview
{
    public static function getOverview(): array
    {
        return [
            Section::make(__('user-entry.infolist.entry_section.heading'))
                ->icon('heroicon-m-user')
                ->description(__('user-entry.infolist.entry_section.description'))
                ->schema([
                    TextEntry::make('sportEvent.name')
                        ->label(__('user-entry.infolist.sport_event_name'))
                        ->size(TextSize::Large),
                    TextEntry::make('sportEvent.date')
                        ->label(__('user-entry.infolist.sport_event_date'))
                        ->icon('heroicon-m-calendar-days')
                        ->dateTime(AppHelper::DATE_FORMAT)
                        ->size(TextSize::Large),
                    TextEntry::make('sportEvent.place')
                        ->label(__('user-entry.infolist.sport_event_place'))
                        ->icon('heroicon-m-map-pin')
                        ->size(TextSize::Large),
                ])
                ->columns(),
            Section::make(__('user-entry.infolist.profile_section.heading'))
                ->icon('heroicon-m-user')
                ->description(__('user-entry.infolist.profile_section.description'))
                ->schema([
                    TextEntry::make('class_name')
                        ->label(__('user-entry.infolist.class_name'))
                        ->badge()
                        ->color('success')
                        ->size(TextSize::Large),
                    TextEntry::make('userRaceProfile.UserRaceFullName')
                        ->label(__('user-entry.infolist.race_profile'))
                        ->size(TextSize::Large),
                    TextEntry::make('note')
                        ->label(__('user-entry.infolist.note'))
                        ->placeholder(__('user-entry.infolist.note_placeholder'))
                        ->size(TextSize::Large),
                    TextEntry::make('club_note')
                        ->icon('heroicon-m-chevron-right')
                        ->label(__('user-entry.infolist.club_note'))
                        ->size(TextSize::Large)
                        ->placeholder(__('user-entry.infolist.club_note_placeholder')),
                    TextEntry::make('requested_start')
                        ->label(__('user-entry.infolist.requested_start'))
                        ->size(TextSize::Large)
                        ->placeholder(__('user-entry.infolist.requested_start_placeholder')),
                    IconEntry::make('rent_si')
                        ->label(__('user-entry.infolist.rent_si'))
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
                        ->label(__('user-entry.infolist.entry_stages'))
                        ->formatStateUsing(fn (string $state): string => str_replace('stage', 'E', $state))
                        ->placeholder(__('user-entry.infolist.entry_stages_placeholder')),
                ])
                ->columns(),
        ];
    }
}
