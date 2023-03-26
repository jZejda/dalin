<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserRaceProfileResource\Pages;
use App\Models\User;
use App\Models\UserRaceProfile;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
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

    protected static ?int $navigationSort = 12;
    protected static ?string $navigationGroup = 'Uživatel';
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'Závodní profil';
    protected static ?string $label = 'Závodní profil';
    protected static ?string $pluralLabel = 'Závodní profily';

    public static function getEloquentQuery(): Builder
    {
        if (Auth::user()->hasRole('super_admin')) {
            return UserRaceProfile::query();
        } else {
            return UserRaceProfile::query()->where('user_id', '=', Auth::user()->id);
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Card::make()
                            ->schema([
                                TextInput::make('reg_number')
                                    ->label('Registrace')
                                    ->disabled(!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN))
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
                                        'H' => 'Muž',
                                        'D' => 'Žena',
                                    ])
                                    ->disabled(!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->required(),

                                TextInput::make('first_name')
                                    ->label('Jméno')
                                    ->disabled(!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->required(),
                                TextInput::make('last_name')
                                    ->label('Příjmení')
                                    ->disabled(!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->required(),
                            ])
                            ->columns(2),

                        Forms\Components\Section::make('Adresa nepovinné')
                            ->schema([
                                TextInput::make('city')
                                    ->label('Město'),
                                TextInput::make('street')
                                    ->label('Ulize číslo domu'),
                                TextInput::make('zip')
                                    ->label('PSČ'),


                                TextInput::make('email')
                                    ->label('E-mail'),
                                TextInput::make('phone')
                                    ->label('Telefon'),
                            ])
                            ->columns(2),
                        Forms\Components\Section::make('SI')
                            ->schema([
                                Forms\Components\TextInput::make('si')
                                    ->label('Si čip')
                                    ->helperText('Preferovaný SI čip')
                                    ->numeric()
                                    ->integer()
                                    ->columnSpan('full'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make('Uživatel')
                            ->schema([
                                TextInput::make('oris_id')
                                    ->label('Oris ID')
                                    ->disabled(!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN)),
                                Select::make('user_id')
                                    ->options(function () {
                                        return User::all()->pluck('user_identification', 'id');
                                    })
                                    ->searchable()
                                    ->disabled(!Auth::user()->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->default(Auth::id())
                                    ->helperText('Automaticky přiřazeno uživateli')
                                    ->disabled(function () {
                                        if (Auth::id() === 1) {
                                            return false;
                                        }
                                        return true;
                                    }),
                            ]),

                        Forms\Components\Section::make('Licence')
                            ->schema([
                                Select::make('licence_ob')
                                    ->label('Licence OB')
                                    ->options(
                                        self::getSportLicenceOptions()
                                    )
                                    ->default('-'),
                                Select::make('licence_lob')
                                    ->label('Licence LOB')
                                    ->options(
                                        self::getSportLicenceOptions()
                                    )
                                    ->default('-'),
                                Select::make('licence_mtbo')
                                    ->label('Licence MTBO')
                                    ->options(
                                        self::getSportLicenceOptions()
                                    )
                                    ->default('-'),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->visible(auth()->user()->hasRole([User::ROLE_SUPER_ADMIN])),
                ])
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

    private static function getSportLicenceOptions(): array {
        return [
            'E' => 'E',
            'A' => 'A',
            'B' => 'B',
            'C' => 'C',
            'D' => 'D',
            'R' => 'R',
            '-' => '-',
        ];
    }
}
