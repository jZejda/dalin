<?php

declare(strict_types=1);

use App\Filament\Resources\SportEvents\Pages\EditSportEvent;
use App\Filament\Resources\SportEvents\RelationManagers\SportMarkersRelationManager;
use App\Models\SportEvent;
use App\Models\SportEventMarker;

use function Pest\Livewire\livewire;

it('renders the points-of-interest relation manager form with the inline location picker', function () {
    actingAsSuperAdmin();

    $event = SportEvent::factory()->create();

    livewire(SportMarkersRelationManager::class, [
        'ownerRecord' => $event,
        'pageClass' => EditSportEvent::class,
    ])
        ->assertSuccessful()
        ->mountTableAction('create')
        ->assertTableActionMounted('create');
});

it('live-syncs the inline location picker into lat/lon', function () {
    actingAsSuperAdmin();

    $event = SportEvent::factory()->create();

    livewire(SportMarkersRelationManager::class, [
        'ownerRecord' => $event,
        'pageClass' => EditSportEvent::class,
    ])
        ->mountTableAction('create')
        ->fillForm(['picked_location' => [50.123456, 14.654321]])
        ->assertSchemaStateSet([
            'lat' => 50.123456,
            'lon' => 14.654321,
        ]);
});

it('pre-fills the inline location picker with the marker\'s existing coordinates when editing', function () {
    actingAsSuperAdmin();

    $event = SportEvent::factory()->create();
    // The lat/lon columns are FLOAT(10), which round-trips at 4 decimal
    // places — use values that survive that exactly, so this test only
    // exercises the picker's hydration, not float column precision.
    $marker = SportEventMarker::factory()->create([
        'sport_event_id' => $event->id,
        'lat' => 50.1235,
        'lon' => 14.6543,
    ]);

    livewire(SportMarkersRelationManager::class, [
        'ownerRecord' => $event,
        'pageClass' => EditSportEvent::class,
    ])
        ->mountTableAction('edit', record: $marker)
        ->assertSchemaStateSet([
            'picked_location' => [50.1235, 14.6543],
        ]);
});
