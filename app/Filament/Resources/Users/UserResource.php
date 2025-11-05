<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use Filament\Support\Enums\TextSize;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use App\Filament\Resources\Users\RelationManagers\UserCreditRelationManager;
use App\Filament\Resources\Users\RelationManagers\UserRaceProfilesRelationManager;
use App\Filament\Resources\Users\Pages\ListUsers;
use Filament\Actions\Action;
use App\Http\Controllers\Cron\Jobs\UserSendPassword;
use App\Mail\UserPasswordSend;
use App\Models\User;
use App\Shared\Helpers\AppHelper;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Schemas\Components\Text;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Uživatelé';

    protected static ?string $label = 'Uživatel';

    protected static ?string $pluralLabel = 'Uživatelé';

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'sm' => 1,
                    'md' => 12,
                ])->schema([
                    // Main column
                    Section::make()
                        ->description(function (): HtmlString {
                            return new HtmlString(__('filament/user.new_user.description', ['help_url' => AppHelper::getPageHelpUrl('jak-pridat-uzivatele.html')]));
                        })
                        ->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('email')
                                ->email()
                                ->required()
                                ->maxLength(255),
                            TextInput::make('payer_variable_symbol')
                                ->label('Variabilní symbol uživatele')
                                ->helperText('Mělo by se jednat o první čísla registrace, tedy přesně 4 číslice.')
                                ->numeric()
                                ->minLength(4)
                                ->maxLength(4),
                            TextInput::make('password')
                                ->password()
                                ->required()
                                ->maxLength(255)
                                ->default(Str::random(10))
                                ->revealable()
                                ->dehydrateStateUsing(static fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                                ->required(static fn (Page $livewire): bool => $livewire instanceof CreateUser)
                                ->dehydrated(static fn (?string $state): bool => filled($state))
                                ->label(
                                    static fn (Page $livewire): string => ($livewire instanceof EditUser) ? 'Nové heslo' : 'Heslo',
                                ),
                        ])
                        ->columns(1)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 8,
                        ]),

                    // Right Column
                    Section::make()
                        ->schema([
                            Text::make(
                                str('**Info:** Uživateli je potřeba přiřadit minimálně jenu z rolí, jinak nebude mít oprávněníní k žádné akci.')
                                ->inlineMarkdown()
                                ->toHtmlString()
                            )->color('warning'),
                            Select::make('roles')
                                ->label('Role')
                                ->multiple()
                                ->searchable()
                                ->relationship('roles', 'name')
                                ->preload()
//                            Select::make('permissions')
//                                ->label('Oprávnění')
//                                ->multiple()
//                                ->searchable()
//                                ->relationship('permissions', 'name')
//                                ->preload(),
                        ])->columnSpan([
                            'sm' => 1,
                            'md' => 4,
                        ]),

                ])->columnSpanFull(),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Jméno')
                    ->size(TextSize::Large)
                    ->color(function (User $model): string {
                        if (!$model->active) {
                            return 'danger';
                        }
                        return 'default';
                    })
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->size(TextSize::Large)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('payer_variable_symbol')
                    ->label('VS')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->separator(',')
                    ->label('Role')
                    ->formatStateUsing(fn (string $state): string => __("app-role.app_role_enum.{$state}"))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Vytvořeno')
                    ->dateTime(AppHelper::DATE_FORMAT),
                TextColumn::make('updated_at')
                    ->label('Upraveno')
                    ->dateTime(AppHelper::DATE_FORMAT),
            ])
            ->filters([
                TernaryFilter::make('active')
                    ->label(__('users.table_filter.users'))
                    ->placeholder(__('users.table_filter.all_users'))
                    ->trueLabel(__('users.table_filter.active_users'))
                    ->falseLabel(__('users.table_filter.disable_users'))
                    ->queries(
                        true: fn (Builder $query) => $query->where('active', '=', 1),
                        false: fn (Builder $query) => $query->where('active', '=', 0),
                        blank: fn (Builder $query) => $query,
                    )
                    ->default(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    self::activeDeactiveUser(),
                    self::resetUserPasswordAction(),
                ]),
            ])
            ->toolbarActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            UserCreditRelationManager::class,
            UserRaceProfilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
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

    private static function resetUserPasswordAction(): Action
    {
        return Action::make('Resetovat heslo')
            ->icon('heroicon-m-arrow-uturn-right')
            ->color('danger')
            ->modalHeading('Nové heslo')
            ->modalDescription(function (User $user): HtmlString {
                return new HtmlString('Resetuje heslo uživateli.<br><br> Po potvrzení se uživatelovi: '. $user->userIdentification .' <strong>zašle e-mail s novým heslem.</strong>');
            })
            ->modalIcon('heroicon-m-arrow-uturn-right')
            ->schema([
                TextInput::make('password')
                    ->label('Nové heslo')
                    ->required()
                    ->readOnly()
                    ->default(Str::random(10)),

            ])
            ->action(function (User $user, array $data): void {
                (new UserSendPassword())->sendNewPassword($user, $data['password'], UserPasswordSend::ACTION_RESET_PASSWORD);
                Notification::make()
                    ->title('Reset hesla')
                    ->body('Nové heslo bylo resetováno a odesláno uživateli na jeho e-mailovou schránku: ' . $user->email . '.')
                    ->success()
                    ->send();
            });
    }

    private static function activeDeactiveUser(): Action
    {
        return Action::make('Změnit stav')
            ->icon('heroicon-m-power')
            ->modalHeading('Změnit stav uživatele')
            ->modalDescription(function (User $user): HtmlString {
                $currentStatus = $user->active ? 'aktivní' : 'neaktivní';
                return new HtmlString("Aktuální stav uživatele {$user->userIdentification} je <strong>{$currentStatus}</strong>.<br>Opravdu chcete změnit jeho stav?");
            })
            ->modalIcon('heroicon-m-power')
            ->schema([
                Select::make('active')
                    ->label('Stav uživatele')
                    ->options([
                        1 => 'Aktivní',
                        0 => 'Neaktivní'
                    ])
                    ->default(fn (User $user) => (int)$user->active)
                    ->required(),
            ])
            ->action(function (User $user, array $data): void {
                $user->active = $data['active'];
                $user->save();

                $status = $data['active'] ? 'aktivován' : 'deaktivován';
                Notification::make()
                    ->title('Změna stavu uživatele')
                    ->body("Uživatel {$user->userIdentification} byl {$status}.")
                    ->success()
                    ->send();
            });
    }
}
