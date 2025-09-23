<?php

namespace App\Filament\Resources\ContentCategories\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\ContentCategories\ContentCategoryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContentCategories extends ListRecords
{
    protected static string $resource = ContentCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
