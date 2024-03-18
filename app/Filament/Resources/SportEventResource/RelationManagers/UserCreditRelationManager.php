<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\RelationManagers;

use App\Models\UserCredit;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class UserCreditRelationManager extends RelationManager
{
    protected static string $relationship = 'userCredit';

    protected static ?string $recordTitleAttribute = 'amouth';

    protected static ?string $title = 'Startovné';

    public array $data_list = [
        'calc_columns' => [
            'amount',
        ],
    ];

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Vytvořeno')
                    ->dateTime('d.m.Y'),
                TextColumn::make('userRaceProfile.UserRaceFullName')
                    ->label('Závodní profil')
                    ->searchable(),
                TextColumn::make('user.userIdentification')
                    ->label('Uživatel'),
                TextColumn::make('amount')
                    ->icon(fn (UserCredit $record): string => $record->amount >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->color(fn (UserCredit $record): string => $record->amount >= 0 ? 'success' : 'danger')
                    ->label(__('user-credit.table.amount_title'))
                    ->summarize(Sum::make())->money('CZK')->label('Celkem'),
            ])
            ->filters([
                //
            ]);
    }
}
