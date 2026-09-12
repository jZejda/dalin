<?php

declare(strict_types=1);

use App\Filament\Clusters\Config\Pages\MapIconGallery;
use App\Filament\Resources\BankTransactions\BankTransactionResource;
use App\Filament\Clusters\Config\Resources\Clubs\ClubResource;
use App\Filament\Resources\ContentCategories\ContentCategoryResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Clusters\Config\Resources\SportClassDefinitions\SportClassDefinitionResource;
use App\Filament\Clusters\Config\Resources\SportEventExports\SportEventExportResource;
use App\Filament\Resources\SportEvents\Pages\EditSportEvent;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Filament\Resources\UserCredits\UserCreditResource;
use App\Filament\Resources\UserEntries\UserEntryResource;
use App\Filament\Clusters\Other\Resources\UserRaceProfiles\UserRaceProfileResource;
use App\Filament\Clusters\Config\Resources\Users\UserResource;
use App\Models\SportEvent;
use Filament\Actions\Testing\TestAction;

use function Pest\Livewire\livewire;

it('can render BankTransactionResource index', function () {
    actingAsSuperAdmin();

    $this->get(BankTransactionResource::getUrl('index'))->assertOk();
});

it('can render ClubResource index', function () {
    actingAsSuperAdmin();

    $this->get(ClubResource::getUrl('index'))->assertOk();
});

it('can render ContentCategoryResource index', function () {
    actingAsSuperAdmin();

    $this->get(ContentCategoryResource::getUrl('index'))->assertOk();
});

it('can render PageResource index', function () {
    actingAsSuperAdmin();

    $this->get(PageResource::getUrl('index'))->assertOk();
});

it('can render PostResource index', function () {
    actingAsSuperAdmin();

    $this->get(PostResource::getUrl('index'))->assertOk();
});

it('can render RoleResource index', function () {
    actingAsSuperAdmin();

    $this->get(RoleResource::getUrl('index'))->assertOk();
});

it('can render SportClassDefinitionResource index', function () {
    actingAsSuperAdmin();

    $this->get(SportClassDefinitionResource::getUrl('index'))->assertOk();
});

it('can render SportEventExportResource index', function () {
    actingAsSuperAdmin();

    $this->get(SportEventExportResource::getUrl('index'))->assertOk();
});

it('can render SportEventResource index', function () {
    actingAsSuperAdmin();

    $this->get(SportEventResource::getUrl('index'))->assertOk();
});

it('can render SportEventResource edit page with the location picker action', function () {
    actingAsSuperAdmin();

    $event = SportEvent::factory()->create();

    $this->get(SportEventResource::getUrl('edit', ['record' => $event]))->assertOk();
});

it('renders the location picker inside the gps_lat suffix action modal', function () {
    actingAsSuperAdmin();

    $event = SportEvent::factory()->create();

    livewire(EditSportEvent::class, ['record' => $event->id])
        ->assertOk()
        ->callAction(TestAction::make('pickLocationOnMap')->schemaComponent('gps_lat'));
});

it('writes the picked location into gps_lat/gps_lon on submit', function () {
    actingAsSuperAdmin();

    $event = SportEvent::factory()->create();

    livewire(EditSportEvent::class, ['record' => $event->id])
        ->callAction(
            TestAction::make('pickLocationOnMap')->schemaComponent('gps_lat'),
            data: ['picked_location' => [50.123456, 14.654321]],
        )
        ->assertSchemaStateSet([
            'gps_lat' => 50.123456,
            'gps_lon' => 14.654321,
        ]);
});

it('can render UserCreditResource index', function () {
    actingAsSuperAdmin();

    $this->get(UserCreditResource::getUrl('index'))->assertOk();
});

it('can render UserEntryResource index', function () {
    actingAsSuperAdmin();

    $this->get(UserEntryResource::getUrl('index'))->assertOk();
});

it('can render UserRaceProfileResource index', function () {
    actingAsSuperAdmin();

    $this->get(UserRaceProfileResource::getUrl('index'))->assertOk();
});

it('can render the map icon gallery page', function () {
    actingAsSuperAdmin();

    $this->get(MapIconGallery::getUrl())->assertOk();
});

it('can render UserResource index', function () {
    actingAsSuperAdmin();

    $this->get(UserResource::getUrl('index'))->assertOk();
});
