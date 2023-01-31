<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserRaceProfileResource\Pages;
use App\Models\User;
use App\Models\UserRaceProfile;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UserRaceProfileResource extends Resource
{
    protected static ?string $model = UserRaceProfile::class;

    protected static ?string $navigationGroup = 'User';
    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function getEloquentQuery(): Builder
    {
        // TODO podle role selektuj vysupt adminum vse ostatnim jeno jejich
        return UserRaceProfile::where('user_id', 1);
    }

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
                            TextInput::make('reg_number')
                                ->label('Registrace')
                                ->unique(ignoreRecord: true)
                                ->required()
                                ->suffixAction(
                                    fn ($state, Closure $set) =>
                                Forms\Components\Actions\Action::make('search_oris_id_by_reg_num')
                                    ->icon('heroicon-o-search')
                                    ->action(function () use ($state, $set) {
                                        if (blank($state))
                                        {
                                            Filament::notify('danger', 'Vyplň prosím Registracni cislo.');
                                            return;
                                        }

                                        try {
                                            $orisResponse = Http::get(
                                                'https://oris.orientacnisporty.cz/API',
                                                [
                                                    'format' => 'json',
                                                    'method' => 'getUser',
                                                    'rgnum' => $state,
                                                ]
                                            )
                                                ->throw()
                                                ->json('Data');


//                                            dd($countryData);

                                        } catch (RequestException $e) {
                                            Filament::notify('danger', 'Nepodařilo se načíst data.');
                                            return;
                                        }
                                        Filament::notify('success', 'ORIS v pořádku vrátil požadovaná data.');

                                        $set('oris_id', $orisResponse['ID'] ?? null);
                                        $set('first_name', $orisResponse['FirstName'] ?? null);
                                        $set('last_name', $orisResponse['LastName'] ?? null);

                                    })
                                ),
                            Select::make('gender')
                                ->label('Pohlaví')
                                ->options([
                                    'H' => 'Muži',
                                    'D' => 'Ženy'
                                ])
                                ->required(),

                            TextInput::make('first_name')
                                ->label('Jméno')
                                ->required(),
                            TextInput::make('last_name')
                                ->label('Příjmení')
                                ->required(), // TODO validation on regex ABM...

                            TextInput::make('email')
                                ->label('E-mail'),
                            TextInput::make('phone')
                                ->label('Telefon'),
                        ])
                        ->columns(2)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 8
                        ]),

                    // Right Column
                    Card::make()
                        ->schema([
                            TextInput::make('oris_id')->label('Oris ID'),
                            Select::make('user_id')
                                ->options(function () {

                                    //if ()

                                    return User::all()->pluck('user_identification', 'id');
                                })
                                ->searchable()
                                ->default(Auth::id())
                                ->disabled(function () {
                                    // $user = User::roles('super_admin')->get();
                                    if (Auth::id() === 1) {
                                        return false;
                                    }
                                    return true;
                                }),
                        ])->columnSpan([
                            'sm' => 1,
                            'md' => 4
                        ]),

                ]),

                Section::make('Vysvětlivka')
                    ->description('Pokud ')
                    ->schema([

                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reg_number')
                    ->label('Registrace')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Uživatel')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('first_name')
                    ->label('Jméno')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->label('Příjmení')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('phone')
                    ->label('Tel')
                    ->searchable()
                    ->sortable()
                    ->copyable(),
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
            'index' => Pages\ListUserRaceProfiles::route('/'),
            'create' => Pages\CreateUserRaceProfile::route('/create'),
            'edit' => Pages\EditUserRaceProfile::route('/{record}/edit'),
        ];
    }
}
