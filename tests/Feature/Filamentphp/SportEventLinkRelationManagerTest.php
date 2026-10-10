<?php

declare(strict_types=1);

use App\Enums\SportEventLinkSource;
use App\Enums\SportEventLinkType;
use App\Filament\Resources\SportEvents\Pages\EditSportEvent;
use App\Filament\Resources\SportEvents\RelationManagers\SportEventLinkRelationManager;
use App\Models\SportEvent;

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
