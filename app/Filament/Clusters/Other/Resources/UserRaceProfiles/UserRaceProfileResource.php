<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Other\Resources\UserRaceProfiles;

use App\Filament\Clusters\Other\OtherCluster;
use App\Shared\Helpers\AppHelper;
use CodeWithDennis\FilamentLucideIcons\Enums\LucideIcon;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\Action;
use Filament\Support\Enums\TextSize;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use App\Filament\Clusters\Other\Resources\UserRaceProfiles\Pages\ListUserRaceProfiles;
use App\Filament\Clusters\Other\Resources\UserRaceProfiles\Pages\CreateUserRaceProfile;
use App\Filament\Clusters\Other\Resources\UserRaceProfiles\Pages\EditUserRaceProfile;
use App\Enums\AppRoles;
use App\Models\User;
use App\Models\UserRaceProfile;
use App\Services\OrisApiService;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class UserRaceProfileResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = UserRaceProfile::class;

    protected static ?string $cluster = OtherCluster::class;

    protected static ?int $navigationSort = 3;

    protected static string | \BackedEnum | null $navigationIcon = LucideIcon::BookUser;

    public static function getNavigationLabel(): string
    {
        return __('user-race-profile.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('user-race-profile.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('user-race-profile.plural_label');
    }

    public static function getEloquentQuery(): Builder
    {
        if (Auth::user()?->hasRole(AppRoles::SuperAdmin->value)) {
            return UserRaceProfile::query();
        } else {
            return UserRaceProfile::query()->where('user_id', '=', Auth::user()?->id);
        }
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('reg_number')
                                    ->label(__('user-race-profile.table.reg_number'))
                                    ->disabled(! Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->suffixAction(
                                        fn ($state, Set $set) => Action::make(
                                            'search_oris_id_by_reg_num'
                                        )
                                            ->icon('heroicon-o-magnifying-glass')
                                            ->action(function () use ($state, $set) {
                                                if (blank($state)) {
                                                    Notification::make()
                                                        ->title(__('user-race-profile.actions.search_oris.validation_title'))
                                                        ->body(__('user-race-profile.actions.search_oris.validation_body'))
                                                        ->danger()
                                                        ->seconds(8)
                                                        ->send();

                                                    return;
                                                }

                                                try {
                                                    $orisResponse = Http::get(
                                                        OrisApiService::ORIS_API_URL,
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
                                                        ->title(__('user-race-profile.actions.search_oris.notification_title'))
                                                        ->body(__('user-race-profile.actions.search_oris.error_body'))
                                                        ->danger()
                                                        ->seconds(8)
                                                        ->send();

                                                    return;
                                                }
                                                Notification::make()
                                                    ->title(__('user-race-profile.actions.search_oris.notification_title'))
                                                    ->body(__('user-race-profile.actions.search_oris.success_body'))
                                                    ->success()
                                                    ->seconds(8)
                                                    ->send();

                                                $set('oris_id', $orisResponse['ID'] ?? null);
                                                $set('first_name', $orisResponse['FirstName'] ?? null);
                                                $set('last_name', $orisResponse['LastName'] ?? null);

                                            })
                                    ),
                                Select::make('gender')
                                    ->label(__('user-race-profile.form.gender'))
                                    ->options([
                                        'H' => __('user-race-profile.form.gender_male'),
                                        'D' => __('user-race-profile.form.gender_female'),
                                    ])
                                    ->disabled(! Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->required(),

                                TextInput::make('first_name')
                                    ->label(__('user-race-profile.table.first_name'))
                                    ->disabled(! Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->required(),
                                TextInput::make('last_name')
                                    ->label(__('user-race-profile.table.last_name'))
                                    ->disabled(! Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->required(),
                            ])
                            ->columns(2),

                        Section::make(__('user-race-profile.form.section_address'))
                            ->schema([
                                TextInput::make('city')
                                    ->label(__('user-race-profile.table.city')),
                                TextInput::make('street')
                                    ->label(__('user-race-profile.form.street')),
                                TextInput::make('zip')
                                    ->label(__('user-race-profile.table.zip')),

                                TextInput::make('email')
                                    ->label(__('user-race-profile.table.email')),
                                TextInput::make('phone')
                                    ->label(__('user-race-profile.table.phone')),
                            ])
                            ->columns(2),
                        Section::make(__('user-race-profile.common.si'))
                            ->schema([
                                TextInput::make('si')
                                    ->label(__('user-race-profile.form.si'))
                                    ->helperText(__('user-race-profile.form.si_helper'))
                                    ->numeric()
                                    ->integer()
                                    ->columnSpan('full'),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('user-race-profile.form.section_user'))
                            ->schema([
                                TextInput::make('oris_id')
                                    ->label(__('user-race-profile.form.oris_id'))
                                    ->disabled(! Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN)),
                                Select::make('user_id')
                                    ->options(function () {
                                        return User::all()->pluck('user_identification', 'id');
                                    })
                                    ->searchable()
                                    ->disabled(! Auth::user()?->hasRole(User::ROLE_SUPER_ADMIN))
                                    ->default(Auth::id())
                                    ->helperText(__('user-race-profile.form.user_id_helper'))
                                    ->disabled(function () {
                                        if (Auth::id() === 1) {
                                            return false;
                                        }

                                        return true;
                                    }),
                            ]),

                        Section::make(__('user-race-profile.form.section_licence'))
                            ->schema([
                                Select::make('licence_ob')
                                    ->label(__('user-race-profile.form.licence_ob'))
                                    ->options(
                                        self::getSportLicenceOptions()
                                    )
                                    ->default('-'),
                                Select::make('licence_lob')
                                    ->label(__('user-race-profile.form.licence_lob'))
                                    ->options(
                                        self::getSportLicenceOptions()
                                    )
                                    ->default('-'),
                                Select::make('licence_mtbo')
                                    ->label(__('user-race-profile.form.licence_mtbo'))
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
                    ->size(TextSize::Large)
                    ->color(function (UserRaceProfile $model): string {
                        if (! $model->active) {
                            return 'danger';
                        }

                        return 'default';
                    })
                    ->description(function (UserRaceProfile $model): ?string {
                        if (! $model->active) {
                            return __('user-race-profile.table.active_until').': '.$model->active_until?->format(
                                AppHelper::DATE_FORMAT
                            );
                        }

                        return null;
                    }),
                TextColumn::make('si')
                    ->label(__('user-race-profile.common.si'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('first_name')
                    ->label(__('user-race-profile.table.first_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('last_name')
                    ->label(__('user-race-profile.table.last_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('user-race-profile.table.email'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),
                TextColumn::make('phone')
                    ->label(__('user-race-profile.table.phone'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                ViewColumn::make('user.name')
                    ->label(__('user-race-profile.table.user-name'))
                    ->view('filament.tables.columns.user-identity')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin->value])),
                ]),
            ])
            ->toolbarActions([
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
            'index' => ListUserRaceProfiles::route('/'),
            'create' => CreateUserRaceProfile::route('/create'),
            'edit' => EditUserRaceProfile::route('/{record}/edit'),
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
