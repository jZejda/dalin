<?php

declare(strict_types=1);

namespace App\Exports;

use App\Enums\EntryStatus;
use App\Models\SportEvent;
use App\Models\UserEntry;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class UsersEntryExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    use Exportable;

    public int $entryId;
    public SportEvent $sportEvent;

    public function forEventEntryId(int $entryId, SportEvent $sportEvent): static
    {
        $this->entryId = $entryId;
        $this->sportEvent = $sportEvent;

        return $this;
    }

    public function query(): Relation|Builder|\Illuminate\Database\Query\Builder
    {
        return UserEntry::query()
            ->where('sport_event_id', '=', $this->entryId)
            ->whereIn('entry_status', [EntryStatus::Create, EntryStatus::Edit]);
    }

    /**
     * @param UserEntry $row
     * @return array
     */
    public function map(mixed $row): array
    {
        return [
            $row->userRaceProfile?->user?->name,
            $row->userRaceProfile?->user?->email,
            $row->userRaceProfile?->first_name,
            $row->userRaceProfile?->last_name,
            $row->userRaceProfile?->reg_number,
            $row->class_name,
            $row->note,
            $row->club_note,
            $row->requested_start,
            $row->si,
            $row->rent_si,
        ];
    }

    public function headings(): array
    {
        return [
            'Uzivatel',
            'E-mail',
            'Příjmení',
            'Jméno',
            'Registrace',
            'Kategorie',
            'Poznámka',
            'Klubová poznámka',
            'Požadavky na strat',
            'Čip',
            'Pujcit cip',
        ];
    }

    public function title(): string
    {
        return 'Akce - ' . $this->sportEvent->name;
    }
}
