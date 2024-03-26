<?php

namespace App\Filament\Resources\TransportOfferResource\Pages;

use App\Filament\Resources\TransportOfferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransportOffer extends EditRecord
{
    protected static string $resource = TransportOfferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
