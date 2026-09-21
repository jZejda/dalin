<?php

declare(strict_types=1);

namespace App\Livewire\SportEvent\Concerns;

use App\Enums\TransportRequestStatus;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;

/**
 * Shared table columns of the two transport request tables
 * (requests for my offers / my requests).
 */
trait HasTransportRequestColumns
{
    private function tripColumn(bool $showDeparture): ViewColumn
    {
        return ViewColumn::make('trip')
            ->label(__('transport.trip_params'))
            ->view('filament.tables.columns.transport-request-trip')
            ->viewData(['showDeparture' => $showDeparture]);
    }

    private function statusColumn(): TextColumn
    {
        return TextColumn::make('status')
            ->label(__('transport.request_status'))
            ->badge()
            ->formatStateUsing(fn (TransportRequestStatus $state): string => $state->label())
            ->color(fn (TransportRequestStatus $state): string => $state->color());
    }
}
