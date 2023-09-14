<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Filament\Resources\UserCreditResource\Pages;
use App\Filament\Resources\UserCreditResource\Widgets\UserCreditStats;
use App\Models\SportEvent;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserRaceProfile;
use App\Shared\Helpers\AppHelper;
use Carbon\Carbon;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

class UserCreditResource extends Resource
{
    protected static ?string $model = UserCredit::class;

    protected static ?int $navigationSort = 40;
    protected static ?string $navigationGroup = 'Správa Financí';
    protected static ?string $navigationIcon = 'heroicon-o-cash';
    protected static ?string $navigationLabel = 'Vyúčtování akcí';
    protected static ?string $label = 'Vyúčtování akcí';
    protected static ?string $pluralLabel = 'Vyúčtování akcí';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Grid::make([
                    'sm' => 1,
                    'md' => 12,
                ])->schema([
                    // Main column
                    Card::make()
                        ->schema([
                            Select::make('user_id')
                                ->label(__('user-credit.user'))
                                ->options(User::all()->pluck('user_identification', 'id'))
                                ->searchable(),
                            Select::make('user_race_profile_id')
                                ->label(__('user-credit.user_profile'))
                                ->options(UserRaceProfile::all()->pluck('reg_number', 'id'))
                                ->searchable(),
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
                                ->options([
                                    UserCredit::SOURCE_CRON => __('user-credit.credit_source_enum.' . UserCredit::SOURCE_CRON),
                                    UserCredit::SOURCE_USER => __('user-credit.credit_source_enum.' . UserCredit::SOURCE_USER),
                                ])
                                ->default(UserCredit::SOURCE_USER)
                                ->disabled()
                                ->searchable()
                                ->required(),

                            // Credit detail
                            Grid::make()->schema([
                                Select::make('credit_type')
                                    ->label(__('user-credit.form.type_title'))
                                    ->options(UserCreditType::enumArray())
                                    ->required(),
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

                        ])
                        ->columns(2)
                        ->columnSpan([
                            'sm' => 1,
                            'md' => 8
                        ]),

                    // Right Column
                    Card::make()
                        ->schema([
                            Select::make('source_user_id')
                                ->label(__('user-credit.user_source_id'))
                                ->options(User::all()->pluck('user_identification', 'id'))
                                ->searchable()
                                ->default(Auth::id())
                                ->disabled(),

                            Select::make('status')
                                ->label(__('user-credit.status'))
                                ->options(UserCreditStatus::enumArray())
                                ->default('done')
                                ->searchable(),

                        ])->columnSpan([
                            'sm' => 1,
                            'md' => 4
                        ]),

                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('user-credit.table.created_at_title'))
                    ->description(function (UserCredit $record): string {
                        return 'id: '. $record->id;
                    })
                    ->dateTime(AppHelper::DATE_FORMAT)
                    ->sortable()
                    ->searchable(),
                TextColumn::make('sportEvent.name')
                    ->label(__('user-credit.table.sport_event_title'))
                    ->description(function (UserCredit $record): string {
                        $description = '';
                        if (!is_null($record->sportEvent?->alt_name)) {
                            $description = $record->sportEvent->alt_name;
                        } else {
                            if (!is_null($record->sportEvent?->id)) {
                                $description = 'interní id závodu: ' . $record->sportEvent->id;
                            }
                        }
                        return $description;
                    }),
                TextColumn::make('user.name')
                    ->label('Uživatel')
                    ->searchable(),
                TextColumn::make('userRaceProfile.reg_number')
                    ->label('RegNumber')
                    ->description(fn (UserCredit $record): string => $record->userRaceProfile->user_race_full_name ?? ''),
                TextColumn::make('amount')
                    ->label(__('user-credit.table.amount_title')),
                TextColumn::make('amount')
                    ->label(__('user-credit.table.amount_title')),
                TextColumn::make('user_credit_notes_count')
                    ->label('Komentářů')
                    ->counts('userCreditNotes'),
                BadgeColumn::make('status')
                    ->label('Status')
                    ->enum(self::getUserCreditStatuses())
                    ->colors([
                        'success' => UserCreditStatus::Done->value,
                        'secondary' => UserCreditStatus::Open->value,
                        'danger' => UserCreditStatus::UnAssign->value,
                    ])
                    ->searchable(),
                TextColumn::make('sourceUser.name')
                    ->label(__('user-credit.table.source_user_title')),
            ])
            ->filters([
                SelectFilter::make('sport_event_id')
                    ->label('Závod')
                    ->options(SportEvent::all()->sortBy('date')->pluck('sport_event_last_cost_calculate', 'id')),
                SelectFilter::make('status')
                    ->options(self::getUserCreditStatuses())
                    ->default(''),
                Filter::make('user_id')
                    ->label('Nená přiřazeno závodníka')
                    ->query(fn (Builder $query): Builder => $query->where('user_id', '=', null))
                    ->default(false),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\Action::make('create_comment')
                    ->icon('heroicon-o-ticket')
                    ->label('Přihlásit na závod.')
                    ->action(function (Collection $records, array $data): void {

                        dd($data);


                    })
                    ->form([
                        Forms\Components\Select::make('authorId')
                            ->label('Author')
                            ->options(User::query()->pluck('name', 'id'))
                            ->required(),
                        Forms\Components\TextInput::make('stiznost')
                            ->label('stiznost')
                    ])
                    ->modalFooter(view('filament.modals.user-credit-comment', (['data' => self::$model])))
                ])

            ])
            ->bulkActions([
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
            'index' => Pages\ListUserCredits::route('/'),
            'create' => Pages\CreateUserCredit::route('/create'),
            'edit' => Pages\EditUserCredit::route('/{record}/edit'),
            'view' => Pages\ViewUserCredit::route('/{record}'),
        ];
    }

    private static function getUserCreditStatuses(): array
    {
        return UserCreditStatus::enumArray();
    }
}
