<?php

declare(strict_types=1);

namespace App\Exports;

use App\Filament\Pages\Actions\ExportUserRaceProfileData;
use App\Models\UserRaceProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Override;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersRaceProfileExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithStyles
{
    use Exportable;

    public string $exportType = ExportUserRaceProfileData::ALL_REGISTRATIONS;

    public function forUserRaceProfile(string $exportType): static
    {
        $this->exportType = $exportType;

        return $this;
    }

    #[Override]public function query(): Relation|Builder|\Illuminate\Database\Query\Builder
    {
        $selectingValues = match($this->exportType) {
            ExportUserRaceProfileData::ALL_REGISTRATIONS => [0,1],
            ExportUserRaceProfileData::ACTIVE_REGISTRATIONS => [1],
            ExportUserRaceProfileData::DEACTIVATED_REGISTRATIONS => [0],
            default => [1,0],
        };

        return UserRaceProfile::query()
            ->whereIn('active', $selectingValues)
            ->orderBy('last_name');
    }

    /**
     * @param UserRaceProfile $row
     */
    #[Override]public function map(mixed $row): array
    {
        return [
            $row->reg_number,
            $row->last_name,
            $row->first_name,
            $row->email,
            $row->phone,
            $row->street,
            $row->city,
            $row->zip,
            $row->si,
            $row->iof_id,
            $row->gender,
            $row->licence_ob,
            $row->licence_lob,
            $row->licence_mtbo,
            $row->created_at?->format('d.m.Y'),
            $row->active ? 'ANO' : 'NE',
            $row->active_until?->format('d.m.Y'),
        ];
    }

    #[Override]public function headings(): array
    {
        return [
            'Registrace',
            'Příjmení',
            'Jméno',
            'E-mail',
            'Telefon',
            'Ulice',
            'Město',
            'PSČ',
            'SI',
            'IOF ID',
            'Gender',
            'Licence OB',
            'Licence LOB',
            'Licence MTBO',
            'Vytvořeno',
            'Aktivní',
            'Aktivní do',

        ];
    }

    #[Override]public function title(): string
    {
        return 'Registrace';
    }

    #[Override]public function styles(Worksheet $sheet): array
    {
        return [
            1    => ['font' => ['bold' => true]],
        ];
    }
}
