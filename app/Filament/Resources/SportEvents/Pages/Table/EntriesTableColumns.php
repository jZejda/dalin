<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Table;

use App\Models\SportClass;
use App\Models\SportEvent;
use App\Models\UserEntry;
use App\Shared\Helpers\AppHelper;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class EntriesTableColumns
{
    /** @return list<TextColumn> */
    public static function make(SportEvent $sportEvent): array
    {
        $isRelay = $sportEvent->isRelayDiscipline();
        $lastEntryDate = $sportEvent->lastEntryDate();

        return [
            TextColumn::make('class_name')
                ->label(__('sport-event.entries_table.class'))
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
                ->label(__('sport-event.entries_table.team'))
                ->formatStateUsing(function ($state, UserEntry $record): string {
                    $slot = $record->relayTeamMember?->slot;
                    if ($state === null) {
                        return __('sport-event.entries_table.team_placeholder');
                    }

                    return $slot !== null ? $state.' (slot '.$slot.')' : $state;
                })
                ->placeholder(__('sport-event.entries_table.team_placeholder'))
                ->visible($isRelay),
            TextColumn::make('userRaceProfile.UserRaceFullName')
                ->label(__('sport-event.entries_table.registration'))
                ->html()
                ->formatStateUsing(fn ($state, UserEntry $record): HtmlString => new HtmlString(
                    (string) view('components.race-profile-identity', [
                        'profile' => $record->userRaceProfile,
                        'size' => 'sm',
                    ])
                ))
                ->searchable(),
            TextColumn::make('note')
                ->label(__('sport-event.entries_table.note'))
                ->limit(15)
                ->tooltip(fn (UserEntry $record): string => $record->note ?? ''),
            TextColumn::make('club_note')
                ->label(__('sport-event.entries_table.club_note'))
                ->limit(15)
                ->tooltip(fn (UserEntry $record): string => $record->club_note ?? ''),
            TextColumn::make('real_start')
                ->label(__('sport-event.entries_table.start'))
                ->dateTime('H:i')
                ->placeholder(__('sport-event.entries_table.start_placeholder')),
            TextColumn::make('rent_si')
                ->label(__('sport-event.entries_table.rent_si')),
            TextColumn::make('entry_stages')
                ->badge()
                ->separator(',')
                ->label(__('sport-event.entries_table.stages'))
                ->formatStateUsing(fn (string $state): string => str_replace('stage', 'E', $state))
                ->searchable(),
            TextColumn::make('entry_status')
                ->label(__('sport-event.entries_table.status'))
                ->badge()
                ->searchable(),
            TextColumn::make('created_at')
                ->label(__('sport-event.entries_table.created_at'))
                ->formatStateUsing(fn (UserEntry $record): string => $record->created_at?->format(AppHelper::DATE_TIME_FORMAT) ?? '')
                ->description(fn (UserEntry $record): ?HtmlString => self::entryDeadlineBadge($record, $sportEvent, $lastEntryDate))
                ->searchable()
                ->sortable(),
        ];
    }

    private static function entryDeadlineBadge(UserEntry $record, SportEvent $sportEvent, ?Carbon $lastEntryDate): ?HtmlString
    {
        $createdAt = $record->created_at;

        if ($createdAt === null) {
            return null;
        }

        if ($lastEntryDate !== null && $createdAt->gt($lastEntryDate)) {
            return new HtmlString(Blade::render(
                '<x-filament::badge color="warning" icon="heroicon-m-exclamation-circle">'
                .e(__('sport-event.entries_table.after_deadline_badge')).'</x-filament::badge>'
            ));
        }

        $term = match (true) {
            $sportEvent->entry_date_1 !== null && $createdAt->lte($sportEvent->entry_date_1) => 1,
            $sportEvent->entry_date_2 !== null && $createdAt->lte($sportEvent->entry_date_2) => 2,
            $sportEvent->entry_date_3 !== null && $createdAt->lte($sportEvent->entry_date_3) => 3,
            default => null,
        };

        if ($term === null) {
            return null;
        }

        return new HtmlString(Blade::render(
            '<x-filament::badge color="success" icon="heroicon-m-check-circle">'
            .e(__('sport-event.entries_table.term_badge', ['term' => $term])).'</x-filament::badge>'
        ));
    }
}
