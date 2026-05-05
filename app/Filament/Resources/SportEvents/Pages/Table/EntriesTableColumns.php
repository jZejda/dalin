<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Table;

use App\Models\SportClass;
use App\Models\SportEvent;
use App\Models\UserEntry;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class EntriesTableColumns
{
    /** @return list<TextColumn> */
    public static function make(SportEvent $sportEvent): array
    {
        $isRelay = $sportEvent->isRelayDiscipline();

        return [
            TextColumn::make('class_name')
                ->label('Kategorie')
                ->description(function (UserEntry $record): string {
                    $sportClass = SportClass::query()
                        ->where('sport_event_id', $record->sport_event_id)
                        ->where('name', $record->class_name)
                        ->first();
                    if ($sportClass === null) {
                        return '';
                    }
                    $parts = array_filter([
                        $sportClass->distance ? $sportClass->distance.'km' : null,
                        $sportClass->climbing ? $sportClass->climbing.'m' : null,
                        $sportClass->controls ? $sportClass->controls.'k' : null,
                    ]);

                    return implode(' | ', $parts);
                })
                ->searchable()
                ->sortable(),
            TextColumn::make('relayTeamMember.relayTeam.name')
                ->label('Tým')
                ->formatStateUsing(function ($state, UserEntry $record): string {
                    $slot = $record->relayTeamMember?->slot;
                    if ($state === null) {
                        return '—';
                    }

                    return $slot !== null ? $state.' (slot '.$slot.')' : $state;
                })
                ->placeholder('—')
                ->visible($isRelay),
            TextColumn::make('userRaceProfile.UserRaceFullName')
                ->label('Registrace')
                ->html()
                ->formatStateUsing(fn ($state, UserEntry $record): HtmlString => new HtmlString(
                    (string) view('components.user-race-profile-badges', [
                        'profiles' => collect([$record->userRaceProfile])->filter(),
                        'size' => 'text-xs',
                    ])
                ))
                ->description(function (UserEntry $record): HtmlString {
                    $name = $record->userRaceProfile?->user?->name;
                    if ($name === null) {
                        return new HtmlString('');
                    }
                    $isCurrentUser = $name === Auth::user()?->name;
                    $classes = $isCurrentUser
                        ? 'inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                        : 'inline-flex items-center rounded px-1.5 py-0.5 text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';

                    return new HtmlString('<span class="'.$classes.'">'.e($name).'</span>');
                })
                ->searchable(),
            TextColumn::make('note')
                ->label('Interní poznámka')
                ->limit(15)
                ->tooltip(fn (UserEntry $record): string => $record->note ?? ''),
            TextColumn::make('club_note')
                ->label('Klubová poznámka')
                ->limit(15)
                ->tooltip(fn (UserEntry $record): string => $record->club_note ?? ''),
            TextColumn::make('real_start')
                ->label('Start v')
                ->dateTime('H:i')
                ->placeholder('—'),
            TextColumn::make('rent_si')
                ->label('Půjčit čip'),
            TextColumn::make('entry_stages')
                ->badge()
                ->separator(',')
                ->label('Etapy')
                ->formatStateUsing(fn (string $state): string => str_replace('stage', 'E', $state))
                ->searchable(),
            TextColumn::make('entry_status')
                ->label('Stav přihlášky')
                ->badge()
                ->searchable(),
            TextColumn::make('created_at')
                ->label('Vytvořeno')
                ->date('d.m.Y')
                ->description(fn (UserEntry $record): string => $record->created_at?->format('H:i') ?? '')
                ->searchable()
                ->sortable(),
        ];
    }
}
