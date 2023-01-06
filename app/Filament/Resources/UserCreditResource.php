<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserCreditResource\Pages;
use App\Filament\Resources\UserCreditResource\Widgets\UserCreditStats;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserRaceProfile;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class UserCreditResource extends Resource
{
    protected static ?string $model = UserCredit::class;

    protected static ?string $navigationIcon = 'heroicon-o-cash';

    protected static ?string $navigationGroup = 'User';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make([
                    'sm' => 1,
                    'md' => 12,
                ])->schema([
                    // Main column
                    Card::make()
                        ->schema([
                            Select::make('user_id')
                                ->label(__('user-credit.user'))
                                ->options(User::all()->pluck('user_identification', 'id'))
                                ->searchable()
                                ->required(),
                            Select::make('user_race_profile_id')
                                ->label(__('user-credit.user_profile'))
                                ->options(UserRaceProfile::all()->pluck('reg_number', 'id'))
                                ->searchable(),
                            Select::make('sport_event_id')
                                ->label(__('user-credit.event_name'))
                                ->options(SportEvent::all()->pluck('name', 'id'))
                                ->searchable(),
                            Select::make('source')
                                ->label(__('user-credit.form.source_title'))
                                ->options([
                                    UserCredit::SOURCE_CRON => __('user-credit.credit_source_enum.' . UserCredit::SOURCE_CRON),
                                    UserCredit::SOURCE_USER => __('user-credit.credit_source_enum.' . UserCredit::SOURCE_USER),
                                ])
                                ->default(UserCredit::SOURCE_USER)
                                ->disabled()
                                ->searchable()
                                ->required(),

                            // Credit detail
                            Grid::make()->schema([
                                Select::make('credit_type')
                                    ->label(__('user-credit.form.type_title'))
                                    ->options([
                                        UserCredit::CREDIT_TYPE_CACHE_IN => __('user-credit.credit_type_enum.' . UserCredit::CREDIT_TYPE_CACHE_IN),
                                        UserCredit::CREDIT_TYPE_CACHE_OUT => __('user-credit.credit_type_enum.' . UserCredit::CREDIT_TYPE_CACHE_OUT),
                                        UserCredit::CREDIT_TYPE_DONATION => __('user-credit.credit_type_enum.' . UserCredit::CREDIT_TYPE_DONATION),
                                    ])
                                    ->required(),
                                TextInput::make('amount')
                                    ->label(__('user-credit.form.amount_title'))
                                    ->required(),
                                Select::make('currency')
                                    ->label(__('user-credit.form.currency_title'))
                                    ->default(UserCredit::CURRENCY_CZK)
                                    ->options([
                                        UserCredit::CURRENCY_CZK => UserCredit::CURRENCY_CZK,
                                        UserCredit::CURRENCY_EUR => UserCredit::CURRENCY_EUR,
                                    ])
                                    ->required()
                                    ->disabled(),
                            ])->columns(3),

                        ])
                        ->columns(2)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 8
                        ]),

                    // Right Column
                    Card::make()
                        ->schema([
                            Select::make('source_user_id')
                                ->label(__('user-credit.user_source_id'))
                                ->options(User::all()->pluck('user_identification', 'id'))
                                ->searchable()
                                ->default(Auth::id())
                                ->disabled()

                            // ....

                        ])->columnSpan([
                            'sm' => 1,
                            'md' => 4
                        ]),

                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('user-credit.table.created_at_title'))
                    ->dateTime('d.m.Y'),
                TextColumn::make('sportEvent.name')
                    ->label(__('user-credit.table.sport_event_title'))
                    ->description(fn (UserCredit $record): string => 'popis transakce'),
                TextColumn::make('userRaceProfile.reg_number')
                    ->label('RegNumber')
                    ->description(fn (UserCredit $record): string => $record->userRaceProfile->user_race_full_name ?? ''),
                TextColumn::make('amount')
                    ->label(__('user-credit.table.amount_title')),
                TextColumn::make('amount')
                    ->label(__('user-credit.table.amount_title')),
                TextColumn::make('sourceUser.name')
                    ->label(__('user-credit.table.source_user_title')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            UserCreditStats::class,
        ];
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserCredits::route('/'),
            'create' => Pages\CreateUserCredit::route('/create'),
            'edit' => Pages\EditUserCredit::route('/{record}/edit'),
        ];
    }
}
