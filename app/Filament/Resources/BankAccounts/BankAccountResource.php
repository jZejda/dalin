<?php

declare(strict_types=1);

namespace App\Filament\Resources\BankAccounts;

use App\Enums\BankConnector;
use App\Filament\Clusters\Config\ConfigCluster;
use App\Filament\Resources\BankAccounts\Pages\CreateBankAccount;
use App\Filament\Resources\BankAccounts\Pages\EditBankAccount;
use App\Filament\Resources\BankAccounts\Pages\ListBankAccounts;
use App\Models\AppSetting;
use App\Models\BankAccount;
use App\Shared\Helpers\AppHelper;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class BankAccountResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = BankAccount::class;

    protected static ?string $cluster = ConfigCluster::class;

    protected static ?int $navigationSort = 20;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'Bankovní napojení';

    protected static ?string $label = 'Bankovní napojení';

    protected static ?string $pluralLabel = 'Bankovní napojení';

    public static function canAccess(): bool
    {
        return AppSetting::isBankModuleEnabled() && parent::canAccess();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Napojení')
                    ->schema([
                        TextInput::make('name')
                            ->label('Název')
                            ->required()
                            ->maxLength(64),
                        Select::make('code')
                            ->label('Banka (konektor)')
                            ->options(BankConnector::enumArray())
                            ->required()
                            ->live()
                            ->native(false)
                            ->disabledOn('edit')
                            ->helperText('Konektor nelze u existujícího napojení měnit — odeber napojení a přidej nové.'),
                        TextInput::make('currency')
                            ->label('Měna')
                            ->required()
                            ->default('CZK')
                            ->maxLength(12),
                        Toggle::make('active')
                            ->label('Napojení je aktivní')
                            ->helperText('Transakce se stahují jen z aktivních napojení.')
                            ->default(false),
                    ])
                    ->columns(2),
                Section::make('Přístupové údaje')
                    ->description('Údaje se ukládají šifrovaně a zpětně se nezobrazují. Při editaci ponech pole prázdné, pokud chceš zachovat uloženou hodnotu.')
                    ->visible(fn (Get $get): bool => filled($get('code')))
                    ->schema(fn (Get $get, ?BankAccount $record, string $operation): array => self::credentialInputs($get('code'), $record, $operation))
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Název')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('code')
                    ->label('Banka')
                    ->badge()
                    ->formatStateUsing(fn (BankConnector $state): string => $state->label()),
                TextColumn::make('currency')
                    ->label('Měna'),
                TextColumn::make('last_synced')
                    ->label('Poslední synchronizace')
                    ->dateTime(AppHelper::DATE_TIME_FORMAT)
                    ->sortable()
                    ->placeholder('nikdy'),
                ToggleColumn::make('active')
                    ->label('Aktivní'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankAccounts::route('/'),
            'create' => CreateBankAccount::route('/create'),
            'edit' => EditBankAccount::route('/{record}/edit'),
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

    /** @return array<int, TextInput> */
    private static function credentialInputs(mixed $code, ?BankAccount $record, string $operation): array
    {
        $connector = $code instanceof BankConnector ? $code : BankConnector::tryFrom((string) ($code ?? ''));

        if ($connector === null) {
            return [];
        }

        $inputs = [];

        foreach ($connector->credentialFields() as $key => $label) {
            $inputs[] = TextInput::make('credentials.'.$key)
                ->label($label)
                ->password()
                ->revealable()
                ->autocomplete('new-password')
                ->required($operation === 'create')
                ->dehydrated(fn (?string $state): bool => filled($state))
                ->placeholder(self::maskedCredential($record?->account_credentials[$key] ?? null))
                ->helperText($operation === 'edit' ? 'Ponech prázdné pro zachování uložené hodnoty.' : null);
        }

        return $inputs;
    }

    private static function maskedCredential(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return '••••'.mb_substr($value, -4);
    }
}
