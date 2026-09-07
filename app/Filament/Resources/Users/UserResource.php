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
use App\Enums\AppRoles;
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

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return __('users.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('users.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('users.plural_label');
    }

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
                            return new HtmlString(__('filament/user.new_user.description', ['help_url' => AppHelper::getPageHelpUrl('jak-zalozit-uzivatele.html')]));
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
                                ->label(__('users.form.payer_variable_symbol'))
                                ->helperText(__('users.form.payer_variable_symbol_helper'))
                                ->minLength(4)
                                ->maxLength(4),
                            TextInput::make('password')
                                ->password()
                                ->required()
                                ->maxLength(255)
                                ->default(Str::random(AppHelper::GENERATED_PASSWORD_LENGTH))
                                ->revealable()
                                ->dehydrateStateUsing(static fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                                ->required(static fn (Page $livewire): bool => $livewire instanceof CreateUser)
                                ->dehydrated(static fn (?string $state): bool => filled($state))
                                ->label(
                                    static function (Page $livewire): string {
                                        if ($livewire instanceof EditUser) {
                                            return __('users.form.new_password');
                                        }

                                        return __('users.form.password');
                                    },
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
                                str(__('users.form.roles_warning'))
                                ->inlineMarkdown()
                                ->toHtmlString()
                            )->color('warning'),
                            Select::make('roles')
                                ->label(__('users.form.roles'))
                                ->multiple()
                                ->searchable()
                                ->relationship('roles', 'name')
                                ->getOptionLabelFromRecordUsing(fn ($record): string => AppRoles::tryFrom($record->name)?->getLabel() ?? $record->name)
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
                    ->label(__('users.table.name'))
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
                    ->label(__('users.table.email'))
                    ->size(TextSize::Large)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('payer_variable_symbol')
                    ->label(__('users.table.variable_symbol'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->separator(',')
                    ->label(__('users.table.roles'))
                    ->formatStateUsing(fn (string $state): string => __("app-role.app_role_enum.{$state}"))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('users.table.created_at'))
                    ->dateTime(AppHelper::DATE_FORMAT),
                TextColumn::make('updated_at')
                    ->label(__('users.table.updated_at'))
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
        return Action::make('reset_password')
            ->label(__('users.actions.reset_password.label'))
            ->icon('heroicon-m-arrow-uturn-right')
            ->color('danger')
            ->modalHeading(__('users.actions.reset_password.modal_heading'))
            ->modalDescription(function (User $user): HtmlString {
                return new HtmlString(__('users.actions.reset_password.modal_description', ['user' => $user->userIdentification]));
            })
            ->modalIcon('heroicon-m-arrow-uturn-right')
            ->schema([
                TextInput::make('password')
                    ->label(__('users.actions.reset_password.field_password'))
                    ->required()
                    ->readOnly()
                    ->default(Str::random(AppHelper::GENERATED_PASSWORD_LENGTH)),

            ])
            ->action(function (User $user, array $data): void {
                (new UserSendPassword())->sendNewPassword($user, $data['password'], UserPasswordSend::ACTION_RESET_PASSWORD);
                Notification::make()
                    ->title(__('users.actions.reset_password.notification_title'))
                    ->body(__('users.actions.reset_password.notification_body', ['email' => $user->email]))
                    ->success()
                    ->send();
            });
    }

    private static function activeDeactiveUser(): Action
    {
        return Action::make('change_status')
            ->label(__('users.actions.change_status.label'))
            ->icon('heroicon-m-power')
            ->modalHeading(__('users.actions.change_status.modal_heading'))
            ->modalDescription(function (User $user): HtmlString {
                $currentStatus = $user->active
                    ? __('users.actions.change_status.current_status_active')
                    : __('users.actions.change_status.current_status_inactive');

                return new HtmlString(__('users.actions.change_status.modal_description', [
                    'user' => $user->userIdentification,
                    'status' => $currentStatus,
                ]));
            })
            ->modalIcon('heroicon-m-power')
            ->schema([
                Select::make('active')
                    ->label(__('users.actions.change_status.field_status'))
                    ->options([
                        1 => __('users.actions.change_status.status_active'),
                        0 => __('users.actions.change_status.status_inactive'),
                    ])
                    ->default(fn (User $user) => (int)$user->active)
                    ->required(),
            ])
            ->action(function (User $user, array $data): void {
                $user->active = $data['active'];
                $user->save();

                $status = $data['active']
                    ? __('users.actions.change_status.activated')
                    : __('users.actions.change_status.deactivated');

                Notification::make()
                    ->title(__('users.actions.change_status.notification_title'))
                    ->body(__('users.actions.change_status.notification_body', [
                        'user' => $user->userIdentification,
                        'status' => $status,
                    ]))
                    ->success()
                    ->send();
            });
    }
}
