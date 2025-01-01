<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Http\Controllers\Cron\Jobs\UserResetPassword;
use App\Models\User;
use App\Shared\Helpers\AppHelper;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Filament\Actions\StaticAction;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;

    protected static ?string $navigationLabel = 'Uživatelé';

    protected static ?string $label = 'Uživatel';

    protected static ?string $pluralLabel = 'Uživatelé';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'sm' => 1,
                    'md' => 12,
                ])->schema([
                    // Main column
                    Section::make()
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
                                ->dehydrateStateUsing(static fn (?string $state): ?string => filled($state) ? Hash::make($state) : null)
                                ->required(static fn (Page $livewire): bool => $livewire instanceof Pages\CreateUser)
                                ->dehydrated(static fn (?string $state): bool => filled($state))
                                ->label(
                                    static fn (Page $livewire): string => ($livewire instanceof Pages\EditUser) ? 'Nové heslo' : 'Heslo',
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
                            Select::make('roles')
                                ->label('Role')
                                ->multiple()
                                ->searchable()
                                ->relationship('roles', 'name')
                                ->preload(),
                            Select::make('permissions')
                                ->label('Oprávnění')
                                ->multiple()
                                ->searchable()
                                ->relationship('permissions', 'name')
                                ->preload(),
                        ])->columnSpan([
                            'sm' => 1,
                            'md' => 4,
                        ]),

                ]),
            ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Jméno')
                    ->size(TextColumnSize::Large)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('E-mail')
                    ->size(TextColumnSize::Large)
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
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    EditAction::make(),
                    self::resetUserPasswordAction(),
                ]),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('name');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UserRaceProfilesRelationManager::class,
            RelationManagers\UserCreditRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
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

    private static function resetUserPasswordAction(): StaticAction
    {
        return Tables\Actions\Action::make('Resetovat heslo')
            ->icon('heroicon-m-arrow-uturn-right')
            ->color('info')
            ->modalHeading('Nové heslo')
            ->modalDescription(function (User $user): HtmlString {
                return new HtmlString('Resetuje heslo uživateli.<br><br> Po potvrzení se uživatelovi: '. $user->userIdentification .' <strong>zašle e-mail s novým heslem.</strong>');
            })
            ->modalIcon('heroicon-m-arrow-uturn-right')
            ->form([
                TextInput::make('password')
                    ->label('Nové heslo')
                    ->required()
                    ->readOnly()
                    ->default(Str::random(10)),

            ])
            ->action(function (User $user, array $data): void {
                (new UserResetPassword())->resetNewPassword($user, $data['password']);
                Notification::make()
                    ->title('Reset hesla')
                    ->body('Nové heslo bylo resetováno a odesláno uživateli na jeho e-mailovou schránku: ' . $user->email . '.')
                    ->success()
                    ->send();
            });
    }
}
