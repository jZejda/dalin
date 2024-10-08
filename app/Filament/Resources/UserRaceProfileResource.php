<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\AppRoles;
use App\Filament\Resources\UserRaceProfileResource\Pages;
use App\Models\User;
use App\Models\UserRaceProfile;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UserRaceProfileResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = UserRaceProfile::class;

    protected static ?int $navigationSort = 35;

    protected static ?string $navigationGroup = 'Uživatel';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationLabel = 'Moje registrace';

    protected static ?string $label = 'Moje registrace';

    protected static ?string $pluralLabel = 'Moje registrace';

    public static function getEloquentQuery(): Builder
    {
        if (Auth::user()?->hasRole(AppRoles::SuperAdmin->value)) {
            return UserRaceProfile::query();
        } else {
            return UserRaceProfile::query()->where('user_id', '=', Auth::user()?->id);
        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                TextInput::make('reg_number')
                                    ->label('Registrace')
                                    ->disabled(! Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->suffixAction(
                                        fn ($state, Set $set) => Forms\Components\Actions\Action::make(
                                            'search_oris_id_by_reg_num'
                                        )
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->action(function () use ($state, $set) {
                                                if (blank($state)) {
                                                    Notification::make()
                                                        ->title('Formulář vstupy')
                                                        ->body('Vyplň prosím Registracni cislo.')
                                                        ->danger()
                                                        ->seconds(8)
                                                        ->send();

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
                                                    Notification::make()
                                                        ->title('ORIS API')
                                                        ->body('Nepodařilo se načíst data.')
                                                        ->danger()
                                                        ->seconds(8)
                                                        ->send();

                                                    return;
                                                }
                                                Notification::make()
                                                    ->title('ORIS API')
                                                    ->body('ORIS v pořádku vrátil požadovaná data.')
                                                    ->success()
                                                    ->seconds(8)
                                                    ->send();

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
                                    ->disabled(! Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->required(),

                                TextInput::make('first_name')
                                    ->label('Jméno')
                                    ->disabled(! Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->required(),
                                TextInput::make('last_name')
                                    ->label('Příjmení')
                                    ->disabled(! Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->required(),
                            ])
                            ->columns(2),

                        Section::make('Adresa nepovinné')
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
                        Section::make('SI')
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
                        Section::make('Uživatel')
                            ->schema([
                                TextInput::make('oris_id')
                                    ->label('Oris ID')
                                    ->disabled(! Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN)),
                                Select::make('user_id')
                                    ->options(function () {
                                        return User::all()->pluck('user_identification', 'id');
                                    })
                                    ->searchable()
                                    ->disabled(! Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->default(Auth::id())
                                    ->helperText('Automaticky přiřazeno uživateli')
                                    ->disabled(function () {
                                        if (Auth::id() === 1) {
                                            return false;
                                        }

                                        return true;
                                    }),
                            ]),

                        Section::make('Licence')
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

    private static function getSportLicenceOptions(): array
    {
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reg_number')
                    ->label(__('user-race-profile.table.reg_number'))
                    ->sortable()
                    ->searchable()
                    ->size(TextColumnSize::Large)
                    ->color(function (UserRaceProfile $model): string {
                        if (! $model->active) {
                            return 'danger';
                        }

                        return 'default';
                    })
                    ->description(function (UserRaceProfile $model): ?string {
                        if (! $model->active) {
                            return __('user-race-profile.table.active_until').': '.$model->active_until?->format(
                                'd.m.Y'
                            );
                        }

                        return null;
                    }),
                TextColumn::make('si')
                    ->label('SI')
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

                TextColumn::make('user.name')
                    ->badge()
                    ->icon('heroicon-o-user')
                    ->label(__('user-race-profile.table.user-name'))
                    ->colors(function (UserRaceProfile $record): array {
                        if ($record->user?->isActive()) {
                            return ['success'];
                        }

                        return ['danger'];
                    })
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin->value])),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
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

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
        ];
    }
}
