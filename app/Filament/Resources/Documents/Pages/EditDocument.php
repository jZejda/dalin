<?php

declare(strict_types=1);

namespace App\Filament\Resources\Documents\Pages;

use App\Filament\Resources\Documents\DocumentResource;
use App\Models\Document;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

class EditDocument extends EditRecord
{
    protected static string $resource = DocumentResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('open')
                ->label(__('document.actions.open.label'))
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn (Document $record): string => $record->publicUrl(), shouldOpenInNewTab: true)
                ->visible(fn (Document $record): bool => $record->file() !== null),
            DeleteAction::make(),
        ];
    }
}
