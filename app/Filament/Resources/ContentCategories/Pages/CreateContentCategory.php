<?php

namespace App\Filament\Resources\ContentCategories\Pages;

use App\Filament\Resources\ContentCategories\ContentCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContentCategory extends CreateRecord
{
    protected static string $resource = ContentCategoryResource::class;
}
