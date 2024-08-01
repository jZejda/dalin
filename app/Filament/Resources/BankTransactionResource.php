<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Models\BankTransaction;
use App\Services\Bank\Enums\TransactionIndicator;
use App\Shared\Helpers\AppHelper;
use Filament\Actions\StaticAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BankTransactionResource extends Resource
{
    protected static ?string $model = BankTransaction::class;

    protected static ?int $navigationSort = 45;

    protected static ?string $navigationGroup = 'Správa Financí';

    protected static ?string $navigationIcon = 'heroicon-m-qr-code';

    protected static ?string $navigationLabel = 'Bankovní výpis';

    protected static ?string $label = 'Bankovní výpis';

    protected static ?string $pluralLabel = 'Bankovní výpis';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {

        return $table
            ->recordClasses(fn (BankTransaction $record) => match ($record->transaction_indicator) {
                TransactionIndicator::Credit => 'opacity-30',
                TransactionIndicator::Debit => 'bg-red-100 dark:bg-red-700',
                default => null,
            })
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
                TextColumn::make('amount')
                    ->icon(fn (BankTransaction $record): ?string => $record->transaction_indicator->getIcon())
                    ->color(fn (BankTransaction $record): ?string => $record->transaction_indicator->getColor())
                    ->label(__('bank-transaction.amount'))
                    ->description(function (BankTransaction $record): string {

                        if ($record->transaction_indicator === TransactionIndicator::Debit) {
                            $amountDirection = __('bank-transaction.transaction_indicator.'.TransactionIndicator::Debit->value);
                        } else {
                            $amountDirection = __('bank-transaction.transaction_indicator.'.TransactionIndicator::Credit->value);
                        }

                        return $amountDirection;

                    }),
                TextColumn::make('variable_symbol')
                    ->label(__('bank-transaction.variable_symbol'))
                    ->sortable()
                    ->searchable(),
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
                //
            ])
            ->defaultSort('date', 'desc')
            ->striped()
            ->actions([
                Tables\Actions\ActionGroup::make(
                    [
                        self::getTableRowAddNoteAction(),
                    ]
                ),

            ])
            ->bulkActions([
                //                Tables\Actions\BulkActionGroup::make([
                //                    Tables\Actions\DeleteBulkAction::make(),
                //                ]),
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
            // 'index' => Pages\ListBankTransactions::route('/'),
            // 'create' => Pages\CreateBankTransaction::route('/create'),
            // 'edit' => Pages\EditBankTransaction::route('/{record}/edit'),
        ];
    }

    private static function getTableRowAddNoteAction(): StaticAction
    {
        return Tables\Actions\Action::make('Popis tranaskce')
            ->icon('heroicon-o-document-text')
            ->color('info')
            ->modalHeading('fsfsdf')
            ->modalDescription('fsfsdfsf')
            ->modalIcon('heroicon-o-document-text')
            ->form([
                TextInput::make('description')
                    ->label('Popis')
                    ->default(fn (BankTransaction $bankTransaction): ?string => $bankTransaction->description),
                TextInput::make('note')
                    ->label('Poznámka')
                    ->default(fn (BankTransaction $bankTransaction): ?string => $bankTransaction->note),
            ])
            ->action(function (BankTransaction $bankTransaction, array $data): void {
                $bankTransaction->description = $data['description'];
                $bankTransaction->note = $data['note'];
                $bankTransaction->save();

                Notification::make()
                    ->title('fsfsf')
                    ->body('fsfsfsdf')
                    ->success()
                    ->send();
            });
    }
}
