<?php

declare(strict_types=1);

namespace App\Filament\Resources\BankTransactions;

use Filament\Schemas\Schema;
use Filament\Support\Enums\TextSize;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;
use App\Enums\UserCreditSource;
use App\Enums\UserCreditStatus;
use App\Enums\UserCreditType;
use App\Filament\Resources\BankTransactions\Pages\CreateBankTransaction;
use App\Filament\Resources\BankTransactions\Pages\EditBankTransaction;
use App\Filament\Resources\BankTransactions\Pages\ListBankTransactions;
use App\Models\AppSetting;
use App\Models\BankTransaction;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use App\Services\Bank\Enums\TransactionIndicator;
use App\Shared\Helpers\AppHelper;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;

class BankTransactionResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = BankTransaction::class;

    protected static ?int $navigationSort = 45;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-m-qr-code';

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation_groups.finance');
    }

    public static function getNavigationLabel(): string
    {
        return __('bank-transaction.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('bank-transaction.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('bank-transaction.plural_label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }

    public static function canAccess(): bool
    {
        return AppSetting::isBankModuleEnabled() && parent::canAccess();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        global $record;
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(__('bank-transaction.id'))
                    ->searchable()
                    ->searchable(),
                TextColumn::make('date')
                    ->label(__('bank-transaction.created_at'))
                    ->dateTime(AppHelper::DATE_FORMAT)
                    //                    ->description(function (BankTransaction $record): string {
                    //                        return $record->date->format('H:i:s');
                    //                    })
                    ->icon('heroicon-o-calendar-days')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('userCredit.id')
                    ->label(__('bank-transaction.user_credit_id'))
                    ->url(function (BankTransaction $bankTransaction): string {
                        if (count($bankTransaction->userCredit) > 0) {
                            return route('filament.admin.resources.user-credits.index', ['tableSearch' => $bankTransaction->userCredit[0]->id]);
                        }

                        return '#';
                    })
                    ->description(function (BankTransaction $bankTransaction): string {
                        return __('bank-transaction.user_credit_count', ['count' => $bankTransaction->userCredit->count()]);
                    }),
                TextColumn::make('amount')
                    ->money('CZK', locale: 'cs')
                    ->icon(fn (BankTransaction $record): ?string => $record->transaction_indicator->getIcon())
                    ->color(fn (BankTransaction $record): ?string => $record->transaction_indicator->getColor())
                    ->size(TextSize::Large)
                    ->label(__('bank-transaction.amount')),
                TextColumn::make('variable_symbol')
                    ->label(__('bank-transaction.variable_symbol'))
                    ->sortable()
                    ->searchable()
                    ->description(function (BankTransaction $record): ?HtmlString {
                        if ($record->bank_account_identifier !== null) {
                            return new HtmlString('<div class="text-sm text-yellow-500 dark:text-yellow-400">' . $record->bank_account_identifier . '</div>');
                        }

                        return null;
                    }),
                TextColumn::make('description')
                    ->label(__('bank-transaction.description'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('note')
                    ->label(__('bank-transaction.note'))
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Filter::make('date')
                    ->schema([
                        DatePicker::make('date')
                            ->label(__('bank-transaction.filters.date_from')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['date'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            );
                    })->indicateUsing(function (array $data): ?string {
                        if (!$data['date']) {
                            return null;
                        }

                        return __('bank-transaction.filters.date_newer', ['date' => Carbon::parse($data['date'])->format(AppHelper::DATE_FORMAT)]);
                    })->default(now()->subDays(7)),
                SelectFilter::make('transaction_indicator')
                    ->label(__('bank-transaction.filters.transaction_indicator'))
                    ->options(TransactionIndicator::enumArray()),
            ])
            ->defaultSort('date', 'desc')
            ->striped()
            ->recordActions([
                ActionGroup::make(
                    [
                        self::getTableRowAddNoteAction(),
                        self::assignTransactionToUserAction()
                            ->visible(function (BankTransaction $record): bool {
                                if ($record->transaction_indicator === TransactionIndicator::Credit) {
                                    return true;
                                }
                                return false;
                            }),
                    ]
                ),

            ])
            ->defaultPaginationPageOption(25)
            ->paginated([10, 25, 50, 100, 'all'])
            ->toolbarActions([
                //                Tables\Actions\BulkActionGroup::make([
                //                    Tables\Actions\DeleteBulkAction::make(),
                //                ]),
            ]);
    }

    private static function getTableRowAddNoteAction(): Action
    {
        return Action::make('edit_description')
            ->label(__('bank-transaction.actions.edit_description.label'))
            ->icon('heroicon-o-document-text')
            ->color('info')
            ->modalHeading(__('bank-transaction.actions.edit_description.modal_heading'))
            ->modalDescription(function (): HtmlString {
                return new HtmlString(__('bank-transaction.actions.edit_description.modal_description'));
            })
            ->modalIcon('heroicon-o-document-text')
            ->schema([
                TextInput::make('description')
                    ->label(__('bank-transaction.description'))
                    ->default(fn (BankTransaction $bankTransaction): ?string => $bankTransaction->description),
                TextInput::make('note')
                    ->label(__('bank-transaction.note'))
                    ->default(fn (BankTransaction $bankTransaction): ?string => $bankTransaction->note),
            ])
            ->action(function (BankTransaction $bankTransaction, array $data): void {
                $bankTransaction->description = $data['description'];
                $bankTransaction->note = $data['note'];
                $bankTransaction->save();

                Notification::make()
                    ->title(__('bank-transaction.actions.edit_description.notification_title'))
                    ->body(__('bank-transaction.actions.edit_description.notification_body'))
                    ->success()
                    ->send();
            });
    }

    private static function assignTransactionToUserAction(): Action
    {
        return Action::make('assign_to_user')
            ->label(__('bank-transaction.actions.assign_to_user.label'))
            ->icon('heroicon-o-user-plus')
            ->color('info')
            ->modalHeading(function (BankTransaction $bankTransaction): string {
                return __('bank-transaction.actions.assign_to_user.modal_heading', ['vs' => $bankTransaction->variable_symbol ?? '---']);
            })
            ->modalDescription(function (): HtmlString {
                return new HtmlString(__('bank-transaction.actions.assign_to_user.modal_description'));
            })
            ->modalIcon('heroicon-o-document-text')
            ->schema([
                Select::make('user_id')
                    ->label(__('user-credit.user'))
                    ->options(User::all()->pluck('user_identification_billing', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('amount')
                    ->label(__('user-credit.amount'))
                    ->default(fn (BankTransaction $bankTransaction): ?string => (string) $bankTransaction->amount)
                    ->required()
                    ->numeric()
                    ->minValue(0),
                MarkdownEditor::make('note')
                    ->label(__('bank-transaction.note'))
                    ->default(fn (BankTransaction $bankTransaction): ?string => $bankTransaction->note)
                    ->required()
                    ->maxLength(255),
            ])
            ->modalContentFooter(fn (BankTransaction $bankTransaction): View => view('filament.actions.modal_footer_add_credit_user', ['bankTransaction' => $bankTransaction]))
            ->action(function (BankTransaction $bankTransaction, array $data): void {

                $userCredit = new UserCredit();
                $userCredit->user_id = $data['user_id'];
                $userCredit->amount = $data['amount'];
                $userCredit->currency = $bankTransaction->currency;
                $userCredit->bank_transaction_id = $bankTransaction->id;
                $userCredit->source = UserCreditSource::User->value;
                $userCredit->status = UserCreditStatus::Done;
                $userCredit->credit_type = UserCreditType::UserDonation;
                $userCredit->source_user_id = Auth::id();
                $userCredit->saveOrFail();

                $userCreditNotes = new UserCreditNote();
                $userCreditNotes->user_credit_id = $userCredit->id;
                $userCreditNotes->note = $data['note'];
                $userCreditNotes->note_user_id = Auth::id();
                $userCreditNotes->internal = 0;
                $userCreditNotes->saveOrFail();

                Notification::make()
                    ->title(__('bank-transaction.actions.assign_to_user.notification_title'))
                    ->body(__('bank-transaction.actions.assign_to_user.notification_body'))
                    ->success()
                    ->send();
            });
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
            'index' => ListBankTransactions::route('/'),
            'create' => CreateBankTransaction::route('/create'),
            'edit' => EditBankTransaction::route('/{record}/edit'),
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
