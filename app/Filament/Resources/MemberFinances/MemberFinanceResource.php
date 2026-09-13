<?php

declare(strict_types=1);

namespace App\Filament\Resources\MemberFinances;

use App\Enums\AppRoles;
use App\Enums\UserParamType;
use App\Filament\Resources\MemberFinances\Actions\AddBulkCreditAction;
use App\Filament\Resources\MemberFinances\Actions\ExportMemberFinancesAction;
use Filament\Support\Enums\TextSize;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\MemberFinances\Pages\ListMemberFinances;
use App\Filament\Resources\MemberFinances\Pages\ViewMemberFinance;
use App\Filament\Clusters\Config\Resources\Users\RelationManagers\UserCreditRelationManager;
use App\Models\User;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Number;

class MemberFinanceResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 10;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static ?string $slug = 'member-finances';

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return __('app.navigation_groups.finance');
    }

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


    public static function infolist(Schema $schema): Schema
    {
        return $schema->columns(1)->components([
            Grid::make(['default' => 1, 'md' => 2])->schema([
                Section::make(__('member-finance.infolist.section_user'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextEntry::make('name')
                            ->label(__('member-finance.infolist.name'))
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold),
                        TextEntry::make('email')
                            ->label(__('member-finance.infolist.email'))
                            ->icon('heroicon-o-envelope')
                            ->copyable(),
                    ]),

                Section::make(__('member-finance.infolist.section_finance'))
                    ->icon('heroicon-o-banknotes')
                    ->schema([
                        TextEntry::make('balance')
                            ->label(__('member-finance.infolist.balance'))
                            ->state(fn (User $record): float => floatval(
                                $record->getParam(UserParamType::UserActualBalance) ?? 0
                            ))
                            ->money('CZK')
                            ->size(TextSize::Large)
                            ->weight(FontWeight::Bold)
                            ->color(fn (User $record): string => floatval(
                                $record->getParam(UserParamType::UserActualBalance) ?? 0
                            ) >= 0 ? 'success' : 'danger'),
                        TextEntry::make('payer_variable_symbol')
                            ->label(__('member-finance.infolist.variable_symbol'))
                            ->icon('heroicon-o-hashtag')
                            ->copyable()
                            ->placeholder('—'),
                    ]),
            ]),
        ]);
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
                ])->with('userRaceProfiles');
            })
            ->columns([
                TextColumn::make('name')
                    ->label(__('member-finance.table.user'))
                    ->html()
                    ->formatStateUsing(fn ($state, User $record): HtmlString => new HtmlString(
                        (string) view('components.user-identity', [
                            'user' => $record,
                            'size' => 'sm',
                        ])
                    ))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('race_profiles_display')
                    ->label(__('member-finance.table.registrations'))
                    ->html()
                    ->state(fn (User $record): string => $record->userRaceProfiles->where('active', true)->pluck('reg_number')->implode(','))
                    ->formatStateUsing(function (User $record): HtmlString {
                        $rows = $record->userRaceProfiles->where('active', true)
                            ->map(fn ($profile): string => (string) view('components.race-profile-compact', [
                                'profile' => $profile,
                                'size' => 'xs',
                            ]))
                            ->implode('');

                        return new HtmlString('<div class="flex flex-col gap-1">'.$rows.'</div>');
                    }),
                TextColumn::make('email')
                    ->label(__('member-finance.table.email'))
                    ->size(TextSize::Medium)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('payer_variable_symbol')
                    ->label(__('member-finance.table.variable_symbol'))
                    ->html()
                    ->size(TextSize::Medium)
                    ->formatStateUsing(fn (string $state): HtmlString => new HtmlString(
                        '<span class="font-mono">'
                        .'<span class="text-gray-400 dark:text-gray-500">'.e(config('site-config.club.extra_membership_fees_prefix')).'</span>'
                        .'<span class="text-gray-950 dark:text-white">'.e($state).'</span>'
                        .'</span>'
                    ))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('computed_balance')
                    ->label(__('member-finance.table.balance'))
                    ->size(TextSize::Large)
                    ->html()
                    ->formatStateUsing(function ($state): HtmlString {
                        $amount = floatval($state ?? 0);
                        $color = $amount >= 0 ? 'text-success-600' : 'text-danger-600';

                        $formatted = Number::format($amount, precision: 2, locale: 'cs') ?: number_format($amount, 2, ',', ' ');

                        return new HtmlString(
                            '<span class="'.$color.'">'.e($formatted).'</span>'
                            .'<span class="text-gray-400 dark:text-gray-500 text-sm ml-1">Kč</span>'
                        );
                    })
                    ->sortable()
                    ->alignEnd(),
                TextColumn::make('active')
                    ->label(__('member-finance.table.active'))
                    ->badge()
                    ->size(TextSize::Medium)
                    ->formatStateUsing(fn (bool $state): string => $state
                        ? __('member-finance.table.status_active')
                        : __('member-finance.table.status_inactive'))
                    ->icon(fn (bool $state): string => $state
                        ? 'heroicon-o-check-circle'
                        : 'heroicon-o-x-circle')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->alignCenter()
                    ->sortable(),
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
            ->toolbarActions([
                AddBulkCreditAction::make(),
                ExportMemberFinancesAction::make(),
            ]);
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
