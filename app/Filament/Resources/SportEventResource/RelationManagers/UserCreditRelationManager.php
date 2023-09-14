<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEventResource\RelationManagers;

use App\Models\UserCredit;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\View\View;

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

    protected function getTableContentFooter(): ?View
    {
        return view('filament.resources.sport-event-resource.tables.user-credit-footer', $this->data_list);
    }

    public static function table(Table $table): Table
    {
        //var_dump(\Route::current());

        //dd('fdsfds');

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
                    ->icon(fn (UserCredit $record): string => $record->amount >= 0 ? 'heroicon-s-trending-up' : 'heroicon-s-trending-down')
                    ->color(fn (UserCredit $record): string => $record->amount >= 0 ? 'success' : 'danger')
                    ->label(__('user-credit.table.amount_title')),
            ])
            ->filters([
                //
            ]);
    }
}
