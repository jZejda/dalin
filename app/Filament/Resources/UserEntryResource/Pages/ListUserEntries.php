<?php

namespace App\Filament\Resources\UserEntryResource\Pages;

use Closure;
use App\Filament\Resources\UserEntryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListUserEntries extends ListRecords
{
    protected static string $resource = UserEntryResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
