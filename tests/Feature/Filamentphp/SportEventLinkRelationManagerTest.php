<?php

declare(strict_types=1);

use App\Enums\SportEventLinkSource;
use App\Enums\SportEventLinkType;
use App\Filament\Resources\SportEvents\Pages\EditSportEvent;
use App\Filament\Resources\SportEvents\RelationManagers\SportEventLinkRelationManager;
use App\Models\SportEvent;
use App\Models\SportEventLink;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Livewire\livewire;

it('shows the link source icon in the links table', function (): void {
    actingAsSuperAdmin();

    $event = SportEvent::factory()->create();
    $link = $event->sportEventLinks()->create([
        'source_url' => 'https://oris.orientacnisporty.cz/files/1_abc.pdf',
        'source_type' => SportEventLinkType::Invitation,
    ]);

    livewire(SportEventLinkRelationManager::class, [
        'ownerRecord' => $event,
        'pageClass' => EditSportEvent::class,
    ])
        ->assertSuccessful()
        ->assertTableColumnStateSet('source', SportEventLinkSource::Oris, $link)
        ->assertSee(SportEventLinkSource::Oris->getLabel());
});

it('uploads a file to DaLin from the create modal', function (): void {
    actingAsSuperAdmin();
    Storage::fake(SportEventLink::FILE_DISK);

    $event = SportEvent::factory()->create();

    livewire(SportEventLinkRelationManager::class, [
        'ownerRecord' => $event,
        'pageClass' => EditSportEvent::class,
    ])
        ->callTableAction('create', data: [
            'name_cz' => 'Pokyny pro závodníky',
            'name_en' => 'Information',
            'link_kind' => 'file',
            'source_path' => UploadedFile::fake()->create('Pokyny pro závodníky.pdf', 120, 'application/pdf'),
            'source_type' => SportEventLinkType::Information->value,
        ])
        ->assertHasNoTableActionErrors();

    $link = $event->sportEventLinks()->sole();

    expect($link->source_url)->toBeNull()
        ->and($link->internal)->toBeTrue()
        ->and($link->source_path)->toMatch('#^'.SportEventLink::FILE_DIRECTORY.'/'.$event->id.'/pokyny-pro-zavodniky-[0-9a-z]{26}\.pdf$#')
        ->and($link->source())->toBe(SportEventLinkSource::Dalin)
        ->and($link->url())->toBe(Storage::disk(SportEventLink::FILE_DISK)->url($link->source_path));

    Storage::disk(SportEventLink::FILE_DISK)->assertExists($link->source_path);
});

it('rejects files a browser could execute', function (string $name, string $mime): void {
    actingAsSuperAdmin();
    Storage::fake(SportEventLink::FILE_DISK);

    $event = SportEvent::factory()->create();

    livewire(SportEventLinkRelationManager::class, [
        'ownerRecord' => $event,
        'pageClass' => EditSportEvent::class,
    ])
        ->callTableAction('create', data: [
            'name_cz' => 'Mapa',
            'name_en' => 'Map',
            'link_kind' => 'file',
            'source_path' => UploadedFile::fake()->create($name, 10, $mime),
            'source_type' => SportEventLinkType::Other->value,
        ])
        ->assertHasTableActionErrors(['source_path']);

    expect($event->sportEventLinks()->count())->toBe(0);
})->with([
    'svg' => ['mapa.svg', 'image/svg+xml'],
    'html' => ['mapa.html', 'text/html'],
]);

it('stores a file with the extension of its detected type, never the client one', function (): void {
    actingAsSuperAdmin();
    Storage::fake(SportEventLink::FILE_DISK);

    $event = SportEvent::factory()->create();

    livewire(SportEventLinkRelationManager::class, [
        'ownerRecord' => $event,
        'pageClass' => EditSportEvent::class,
    ])
        ->callTableAction('create', data: [
            'name_cz' => 'Plánek',
            'name_en' => 'Plan',
            'link_kind' => 'file',
            'source_path' => UploadedFile::fake()->image('Plánek.JPEG'),
            'source_type' => SportEventLinkType::CompetitionCentreMap->value,
        ])
        ->assertHasNoTableActionErrors();

    expect($event->sportEventLinks()->sole()->source_path)->toMatch('#/planek-[0-9a-z]{26}\.jpg$#');
});

it('rejects a script disguised as plain text', function (): void {
    actingAsSuperAdmin();
    Storage::fake(SportEventLink::FILE_DISK);

    $event = SportEvent::factory()->create();

    livewire(SportEventLinkRelationManager::class, [
        'ownerRecord' => $event,
        'pageClass' => EditSportEvent::class,
    ])
        ->callTableAction('create', data: [
            'name_cz' => 'Poznámky',
            'name_en' => 'Notes',
            'link_kind' => 'file',
            'source_path' => UploadedFile::fake()->createWithContent('shell.php', 'plain looking text'),
            'source_type' => SportEventLinkType::Other->value,
        ])
        ->assertHasTableActionErrors(['source_path']);

    expect($event->sportEventLinks()->count())->toBe(0);
});

it('deletes the uploaded file when the link switches to a url or is deleted', function (): void {
    Storage::fake(SportEventLink::FILE_DISK);

    $event = SportEvent::factory()->create();
    $path = SportEventLink::FILE_DIRECTORY.'/'.$event->id.'/pokyny.pdf';
    Storage::disk(SportEventLink::FILE_DISK)->put($path, '%PDF-1.4');

    $link = $event->sportEventLinks()->create([
        'source_path' => $path,
        'source_type' => SportEventLinkType::Information,
        'internal' => true,
    ]);
    $link->update(['source_path' => null, 'source_url' => 'https://example.org/pokyny']);

    Storage::disk(SportEventLink::FILE_DISK)->assertMissing($path);

    Storage::disk(SportEventLink::FILE_DISK)->put($path, '%PDF-1.4');
    $link->update(['source_path' => $path, 'source_url' => null]);
    $link->delete();

    Storage::disk(SportEventLink::FILE_DISK)->assertMissing($path);
});

it('keeps ORIS links as web links', function (): void {
    actingAsSuperAdmin();

    $event = SportEvent::factory()->create();
    $link = $event->sportEventLinks()->create([
        'external_key' => 12345,
        'source_url' => 'https://oris.orientacnisporty.cz/files/1_abc.pdf',
        'source_type' => SportEventLinkType::Invitation,
    ]);

    livewire(SportEventLinkRelationManager::class, [
        'ownerRecord' => $event,
        'pageClass' => EditSportEvent::class,
    ])
        ->mountTableAction('edit', $link)
        ->assertTableActionDataSet(['link_kind' => 'url'])
        ->assertFormFieldIsDisabled('link_kind');
});
