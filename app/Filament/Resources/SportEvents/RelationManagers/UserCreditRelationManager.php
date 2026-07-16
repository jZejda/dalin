<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\RelationManagers;

use App\Shared\Helpers\AppHelper;
use App\Models\UserCredit;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class UserCreditRelationManager extends RelationManager
{
    protected static string $relationship = 'userCredit';

    protected static ?string $recordTitleAttribute = 'amouth';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('sport-event.relation_credits.title');
    }

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
                    ->label(__('sport-event.relation_credits.table.created_at'))
                    ->dateTime(AppHelper::DATE_FORMAT),
                TextColumn::make('userRaceProfile.UserRaceFullName')
                    ->label(__('sport-event.relation_credits.table.user_race_profile'))
                    ->searchable(),
                TextColumn::make('user.userIdentification')
                    ->label(__('sport-event.relation_credits.table.user')),
                TextColumn::make('amount')
                    ->icon(fn (UserCredit $record): string => $record->amount >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                    ->color(fn (UserCredit $record): string => $record->amount >= 0 ? 'success' : 'danger')
                    ->label(__('user-credit.table.amount_title'))
                    ->summarize(Sum::make())->money('CZK')->label(__('sport-event.relation_credits.table.amount_total')),
            ])
            ->filters([
                //
            ]);
    }
}
