<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserCreditResource\Pages;
use App\Filament\Resources\UserCreditResource\RelationManagers;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserRaceProfile;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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
                                ->searchable()
                                ->required(),
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
                //
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
