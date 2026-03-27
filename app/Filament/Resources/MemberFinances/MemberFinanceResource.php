<?php

declare(strict_types=1);

namespace App\Filament\Resources\MemberFinances;

use App\Enums\AppRoles;
use App\Enums\UserParamType;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\MemberFinances\Pages\ListMemberFinances;
use App\Filament\Resources\MemberFinances\Pages\ViewMemberFinance;
use App\Filament\Resources\Users\RelationManagers\UserCreditRelationManager;
use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class MemberFinanceResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 10;

    protected static string|\UnitEnum|null $navigationGroup = 'Správa Financí';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $slug = 'member-finances';

    public static function getNavigationLabel(): string
    {
        return __('member-finance.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('member-finance.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('member-finance.plural_label');
    }

    private static function hasFinanceAccess(): bool
    {
        return auth()->user()?->hasRole([
            AppRoles::SuperAdmin,
            AppRoles::BillingSpecialist,
            AppRoles::ClubAdmin,
        ]) ?? false;
    }

    public static function canViewAny(): bool
    {
        return self::hasFinanceAccess();
    }

    public static function canView(Model $record): bool
    {
        return self::hasFinanceAccess();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('member-finance.form.section_title'))
                ->schema([
                    Grid::make(3)->schema([
                        TextInput::make('name')
                            ->label(__('member-finance.form.name'))
                            ->disabled(),
                        TextInput::make('email')
                            ->label(__('member-finance.form.email'))
                            ->disabled(),
                        TextInput::make('payer_variable_symbol')
                            ->label(__('member-finance.form.payer_variable_symbol'))
                            ->disabled(),
                        Toggle::make('active')
                            ->label(__('member-finance.form.active'))
                            ->disabled(),
                        Placeholder::make('balance')
                            ->label(__('member-finance.form.balance'))
                            ->content(function (User $record): HtmlString {
                                $balance = floatval($record->getParam(UserParamType::UserActualBalance) ?? 0);
                                $formatted = number_format($balance, 2, ',', ' ') . ' CZK';
                                $color = $balance >= 0 ? 'text-success-600' : 'text-danger-600';

                                return new HtmlString("<span class=\"font-bold {$color}\">{$formatted}</span>");
                            }),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                return $query->addSelect([
                    'users.*',
                    'computed_balance' => DB::table('user_credits')
                        ->selectRaw('COALESCE(SUM(amount), 0)')
                        ->whereColumn('user_id', 'users.id'),
                ]);
            })
            ->columns([
                TextColumn::make('name')
                    ->label(__('member-finance.table.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('member-finance.table.email'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('payer_variable_symbol')
                    ->label(__('member-finance.table.variable_symbol'))
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('active')
                    ->label(__('member-finance.table.active'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('computed_balance')
                    ->label(__('member-finance.table.balance'))
                    ->money('CZK')
                    ->color(fn (User $record): string => floatval($record->computed_balance ?? 0) >= 0 ? 'success' : 'danger')
                    ->sortable()
                    ->alignEnd(),
            ])
            ->defaultSort('name', 'asc')
            ->defaultPaginationPageOption(25)
            ->filters([
                SelectFilter::make('active')
                    ->label(__('member-finance.filters.membership_status'))
                    ->options([
                        '1' => __('member-finance.filters.active'),
                        '0' => __('member-finance.filters.inactive'),
                    ]),
                Filter::make('negative_balance')
                    ->label(__('member-finance.filters.negative_balance'))
                    ->query(fn (Builder $query): Builder => $query->whereRaw(
                        '(SELECT COALESCE(SUM(amount), 0) FROM user_credits WHERE user_id = users.id) < 0'
                    ))
                    ->toggle(),
                Filter::make('zero_balance')
                    ->label(__('member-finance.filters.zero_balance'))
                    ->query(fn (Builder $query): Builder => $query->whereRaw(
                        '(SELECT COALESCE(SUM(amount), 0) FROM user_credits WHERE user_id = users.id) = 0'
                    ))
                    ->toggle(),
                Filter::make('positive_balance')
                    ->label(__('member-finance.filters.positive_balance'))
                    ->query(fn (Builder $query): Builder => $query->whereRaw(
                        '(SELECT COALESCE(SUM(amount), 0) FROM user_credits WHERE user_id = users.id) > 0'
                    ))
                    ->toggle(),
            ])
            ->recordUrl(fn (User $record): string => static::getUrl('view', ['record' => $record]))
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getRelations(): array
    {
        return [
            UserCreditRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMemberFinances::route('/'),
            'view'  => ViewMemberFinance::route('/{record}'),
        ];
    }
}
