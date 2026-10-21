<?php

declare(strict_types=1);

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('publicList')
                ->label(__('document.actions.public_list.label'))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(route('documents.index'), shouldOpenInNewTab: true),
            CreateAction::make(),
        ];
    }
}
