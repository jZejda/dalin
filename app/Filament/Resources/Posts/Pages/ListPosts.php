<?php

namespace App\Filament\Resources\Posts\Pages;

use Filament\Actions\CreateAction;
use App\Filament\Resources\Posts\PostResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
