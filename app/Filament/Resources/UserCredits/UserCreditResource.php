<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserCredits;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use App\Filament\Resources\UserCredits\Actions\AddUserCreditNoteModal;
use App\Filament\Resources\UserCredits\Pages\ListUserCredits;
use App\Enums\AppRoles;
use App\Enums\UserCreditSource;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Filament\Resources\UserCredits\Widgets\UserCreditStats;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserRaceProfile;
use App\Shared\Helpers\AppHelper;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;

class UserCreditResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = UserCredit::class;

    protected static ?int $navigationSort = 40;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-banknotes';

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation_groups.finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('user-credit.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('user-credit.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('user-credit.plural_label');
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
                        ->schema([

                            // Credit detail
                            Grid::make()->schema([
                                Select::make('credit_type')
                                    ->label(__('user-credit.form.type_title'))
                                    ->options(UserCreditType::enumArray())
                                    ->required()
                                    ->live(),
                                TextInput::make('amount')
                                    ->label(__('user-credit.form.amount_title'))
                                    ->required(),
                                Select::make('currency')
                                    ->label(__('user-credit.form.currency_title'))
                                    ->default(UserCredit::CURRENCY_CZK)
                                    ->options([
                                        UserCredit::CURRENCY_CZK => UserCredit::CURRENCY_CZK,
                                        UserCredit::CURRENCY_EUR => UserCredit::CURRENCY_EUR,
                                    ])
                                    ->required()
                                    ->disabled(),
                            ])->columns(3),

                            // Users
                            Grid::make()->schema([
                                Select::make('user_id')
                                    ->label(__('user-credit.user'))
                                    ->options(User::all()->pluck('user_identification', 'id'))
                                    ->required()
                                    ->searchable(),
                                Select::make('related_user_id')
                                    ->label(__('user-credit.related_user_profile'))
                                    ->options(User::all()->pluck('user_identification', 'id'))
                                    ->hint(__('user-credit.form.related_user_hint'))
                                    ->hintColor('warning')
                                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('user-credit.form.warning_tooltip'))
                                    ->visible(function (Get $get): bool {
                                        if ($get('credit_type') === UserCreditType::TransferCreditBetweenUsers->value) {
                                            return true;
                                        }

                                        return false;
                                    })
                                    ->required(function (Get $get): bool {
                                        if ($get('credit_type') === UserCreditType::TransferCreditBetweenUsers->value) {
                                            return true;
                                        }

                                        return false;
                                    })
                                    ->searchable(),
                                Select::make('user_race_profile_id')
                                    ->label(__('user-credit.user_profile'))
                                    ->options(UserRaceProfile::all()->pluck('user_race_full_name', 'id'))
                                    ->searchable(),
                            ])->columns(1),

                            Select::make('sport_event_id')
                                ->label(__('user-credit.event_name'))
                                ->options(
                                    SportEvent::all()
                                        ->where('date', '>', Carbon::now()->subMonths(12)->format(AppHelper::MYSQL_DATE_TIME))
                                        ->sortByDesc('date')
                                        ->pluck('sport_event_oris_compact_title', 'id')
                                )->searchable(),
                            Select::make('source')
                                ->label(__('user-credit.form.source_title'))
                                ->default(UserCreditSource::User->value)
                                ->options(UserCreditSource::enumArray())
                                ->disabled()
                                ->required(),

                        ])
                        ->columns(1)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 8,
                        ]),

                    // Right Column
                    Section::make()
                        ->schema([
                            Select::make('source_user_id')
                                ->label(__('user-credit.user_source_id'))
                                ->options(function () {
                                    $users = User::with('roles')->whereHas('roles', function ($q) {
                                        $q->whereIn('name', [AppRoles::BillingSpecialist->value, AppRoles::SuperAdmin->value]);
                                    })->get();

                                    return $users->pluck('user_identification', 'id');
                                })
                                ->default(Auth::user()?->id)
                                ->searchable(),

                            Select::make('status')
                                ->label(__('user-credit.status'))
                                ->options(UserCreditStatus::enumArray())
                                ->default('done')
                                ->searchable(),

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
                TextColumn::make('id')
                    ->label(__('user-credit.id'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('user-credit.table.created_at_title'))
                    ->dateTime(AppHelper::DATE_FORMAT)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sportEvent.date')
                    ->label(__('user-credit.table.sport_event_date'))
                    ->icon('heroicon-o-calendar-days')
                    ->dateTime(AppHelper::DATE_FORMAT)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sportEvent.name')
                    ->label(__('user-credit.table.sport_event_title'))
                    ->description(function (UserCredit $record): string {
                        $description = '';
                        if (! is_null($record->sportEvent?->alt_name)) {
                            $description = $record->sportEvent->alt_name;
                        } else {
                            if (! is_null($record->sportEvent?->id)) {
                                $description = __('user-credit.table.event_internal_id', ['id' => $record->sportEvent->id]);
                            }
                        }

                        return $description;
                    }),
                ViewColumn::make('user.name')
                    ->label(__('user-race-profile.table.user-name'))
                    ->view('filament.tables.columns.user-identity')
                    ->searchable(),
                TextColumn::make('userRaceProfile.reg_number')
                    ->label(__('user-credit.table.registration'))
                    ->description(fn (UserCredit $record): string => $record->userRaceProfile->user_race_full_name ?? ''),
                TextColumn::make('amount')
                    ->icon(fn (UserCredit $record): ?string => $record->credit_type->getIcon())
                    ->color(fn (UserCredit $record): ?string => $record->credit_type->getColor())
                    ->label(__('user-credit.table.amount_title'))
                    ->description(function (UserCredit $record): null|string|HtmlString {
                        if ($record->relatedUser !== null) {
                            if ($record->amount < 0) {
                                $amountDirection = __('user-credit.table.for_user');
                            } else {
                                $amountDirection = __('user-credit.table.from_user');
                            }

                            return $amountDirection.$record->relatedUser->name;
                        }

                        if ($record->bankTransaction !== null) {
                            return new HtmlString('<a href="' . route('filament.admin.resources.bank-transactions.index') .'">' . __('user-credit.table.transaction_link', ['id' => $record->bankTransaction->id]) . '</a>');
                        }

                        return null;
                    }),
                ViewColumn::make('user_entry')
                    ->label(__('user-credit.table.comments'))
                    ->view('filament.tables.columns.user-credit-comments-count'),
                TextColumn::make('status')
                    ->label(__('user-credit.status'))
                    ->badge()
                    ->formatStateUsing(fn (UserCreditStatus $state): string => __("sport-event.type_enum_credit_status.{$state->value}"))
                    ->colors(self::getUserCreditStatuses())
                    ->searchable(),
                ViewColumn::make('sourceUser.name')
                    ->label(__('user-credit.table.source_user_title'))
                    ->view('filament.tables.columns.user-badge'),
            ])
            ->defaultPaginationPageOption(25)
            ->persistSortInSession()
            ->defaultSort('updated_at', 'desc')
            ->filters([
                SelectFilter::make('sport_event_id')
                    ->label(__('user-credit.filters.sport_event'))
                    ->options(SportEvent::all()->sortBy('date')->pluck('sport_event_last_cost_calculate', 'id')),
                SelectFilter::make('status')
                    ->options(self::getUserCreditStatuses())
                    ->default(''),
                Filter::make('user_id')
                    ->label(__('user-credit.filters.unassigned_racer'))
                    ->query(fn (Builder $query): Builder => $query->where('user_id', '=', null))
                    ->default(false),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make()
                        ->modalWidth(Width::SevenExtraLarge),
                    (new AddUserCreditNoteModal())->getAction(),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                //Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    private static function getPluckUsers(): Collection
    {
        return User::all()->pluck('name', 'id');
    }

    public static function getWidgets(): array
    {
        return [
            UserCreditStats::class,
        ];
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
            'index' => ListUserCredits::route('/'),
        ];
    }

    private static function getUserCreditStatuses(): array
    {
        return UserCreditStatus::enumArray();
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
