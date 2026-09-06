<?php

declare(strict_types=1);

use App\Filament\Resources\SportEvents\Pages\EditSportEvent;
use App\Filament\Resources\SportEvents\RelationManagers\SportMarkersRelationManager;
use App\Models\SportEvent;

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
