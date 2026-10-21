<?php

declare(strict_types=1);

use App\Enums\EntryStatus;
use App\Exports\UsersEntryExport;
use App\Exports\UsersRaceProfileExport;
use App\Filament\Resources\MemberFinances\Pages\ListMemberFinances;
use App\Filament\Resources\SportEvents\Pages\EditSportEvent;
use App\Filament\Resources\SportEvents\RelationManagers\UserEntryRelationManager;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserEntry;
use App\Models\UserRaceProfile;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel as ExcelType;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

use function Pest\Livewire\livewire;

function loadXlsxSheet(string $raw): Worksheet
{
    $path = tempnam(sys_get_temp_dir(), 'xlsx');
    file_put_contents($path, $raw);

    try {
        return IOFactory::load($path)->getActiveSheet();
    } finally {
        unlink($path);
    }
}

function createEntry(SportEvent $event, UserRaceProfile $profile, EntryStatus $status): UserEntry
{
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    $entry = UserEntry::create([
        'sport_event_id' => $event->id,
        'class_definition_id' => 999,
        'user_race_profile_id' => $profile->id,
        'class_name' => 'H21',
        'entry_status' => $status->value,
        'rent_si' => false,
        'entry_created' => now(),
    ]);

    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    return $entry;
}

it('exports active entries of an event to xlsx', function (): void {
    $event = SportEvent::factory()->create(['name' => 'Oblastní žebříček']);
    $profile = UserRaceProfile::factory()->create(['last_name' => 'Novák']);
    createEntry($event, $profile, EntryStatus::Create);
    createEntry($event, UserRaceProfile::factory()->create(), EntryStatus::Cancel);

    $sheet = loadXlsxSheet(Excel::raw((new UsersEntryExport())->forEventEntryId($event->id, $event), ExcelType::XLSX));

    expect($sheet->getTitle())->toBe('Akce - Oblastní žebříček')
        ->and($sheet->getHighestRow())->toBe(2)
        ->and($sheet->getCell('A1')->getValue())->toBe('Uživatel')
        ->and($sheet->getCell('D2')->getValue())->toBe('Novák');
});

it('exports race profiles to xlsx with a bold heading row', function (): void {
    UserRaceProfile::factory()->count(2)->create(['active' => true]);

    $sheet = loadXlsxSheet(Excel::raw(new UsersRaceProfileExport(), ExcelType::XLSX));

    expect($sheet->getTitle())->toBe('Registrace')
        ->and($sheet->getHighestRow())->toBe(3)
        ->and($sheet->getCell('A1')->getValue())->toBe('Registrace')
        ->and($sheet->getStyle('A1')->getFont()->getBold())->toBeTrue();
});

it('downloads the entries export from the entries relation manager', function (): void {
    actingAsSuperAdmin();

    $event = SportEvent::factory()->create();
    $entry = createEntry($event, UserRaceProfile::factory()->create(), EntryStatus::Create);
    $entry->update(['rent_si' => true]);

    livewire(UserEntryRelationManager::class, [
        'ownerRecord' => $event,
        'pageClass' => EditSportEvent::class,
    ])
        ->selectTableRecords([$entry->getKey()])
        ->callAction(TestAction::make('exportToFile')->table()->bulk(), data: [
            'export' => [
                'filename' => 'export_prihlasek',
                'writer_type' => ExcelType::XLSX,
            ],
        ])
        ->assertHasNoFormErrors()
        ->assertFileDownloaded('export_prihlasek.xlsx');
});

it('downloads the member finances export', function (): void {
    actingAsSuperAdmin();

    $users = User::factory()->count(2)->create();

    livewire(ListMemberFinances::class)
        ->selectTableRecords($users->modelKeys())
        ->callAction(TestAction::make('exportMemberFinances')->table()->bulk(), data: [
            'export' => [
                'filename' => 'export_clenu',
                'writer_type' => ExcelType::CSV,
            ],
        ])
        ->assertHasNoFormErrors()
        ->assertFileDownloaded('export_clenu.csv');
});
