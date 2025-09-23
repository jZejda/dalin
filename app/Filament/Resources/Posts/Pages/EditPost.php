<?php

namespace App\Filament\Resources\Posts\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use App\Filament\Resources\Posts\PostResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
