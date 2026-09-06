<?php

declare(strict_types=1);

use App\Filament\Clusters\Config\Pages\MapIconGallery;
use App\Filament\Resources\BankTransactions\BankTransactionResource;
use App\Filament\Resources\Clubs\ClubResource;
use App\Filament\Resources\ContentCategories\ContentCategoryResource;
use App\Filament\Resources\Pages\PageResource;
use App\Filament\Resources\Posts\PostResource;
use App\Filament\Resources\Roles\RoleResource;
use App\Filament\Resources\SportClassDefinitions\SportClassDefinitionResource;
use App\Filament\Resources\SportEventExports\SportEventExportResource;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Filament\Resources\UserCredits\UserCreditResource;
use App\Filament\Resources\UserEntries\UserEntryResource;
use App\Filament\Clusters\Other\Resources\UserRaceProfiles\UserRaceProfileResource;
use App\Filament\Resources\Users\UserResource;

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
