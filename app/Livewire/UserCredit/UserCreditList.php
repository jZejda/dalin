<?php

namespace App\Livewire\UserCredit;

use App\Enums\AppRoles;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use App\Shared\Helpers\AppHelper;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UserCreditList extends Component implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    public string $activeTab = '';

    public function table(Table $table): Table
    {
        return $table
            ->query(UserCredit::query()->where('user_id', '=', auth()->user()?->id))
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('user-credit.table.created_at_title'))
                    ->dateTime('d.m.Y')
                    ->description(function (UserCredit $record): string {
                        return 'ID:'.$record->id;
                    })
                    ->sortable(),
                TextColumn::make('sportEvent.date')
                    ->label(__('user-credit.table.sport_event_date'))
                    ->icon('heroicon-o-calendar-days')
                    ->dateTime(AppHelper::DATE_FORMAT)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('sportEvent.name')
                    ->label(__('user-credit.table.sport_event_title'))
                    ->url(function (UserCredit $record): ?string {
                        if (! is_null($record->sport_event_id)) {
                            return route('filament.admin.resources.sport-events.entry', ['record' => $record->sport_event_id]);
                        }

                        return null;
                    })
                    ->description(fn (UserCredit $record): string => $record->sportEvent?->alt_name !== null ? $record->sportEvent->alt_name : 'nemá vazbu na akci/závod')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('userRaceProfile.reg_number')
                    ->label('Registrace závodníka')
                    ->description(fn (UserCredit $record): string => $record->userRaceProfile->user_race_full_name ?? '')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('amount')
                    ->icon(fn (UserCredit $record): ?string => $record->credit_type->getIcon())
                    ->color(fn (UserCredit $record): ?string => $record->credit_type->getColor())
                    ->numeric(decimalPlaces: 0)
                    ->label(__('user-credit.table.amount_title'))
                    ->money('CZK')
                    ->description(function (UserCredit $record): ?string {
                        if ($record->relatedUser !== null) {
                            if ($record->amount < 0) {
                                $amountDirection = __('user-credit.table.for_user');
                            } else {
                                $amountDirection = __('user-credit.table.from_user');
                            }

                            return $amountDirection.$record->relatedUser->name;
                        }

                        return null;
                    })
                    ->summarize(Sum::make())->money('CZK')->label('Celkem'),
                ViewColumn::make('user_entry')
                    ->label('Komentářů')
                    ->view('filament.tables.columns.user-credit-comments-count'),
                TextColumn::make('sourceUser.name')
                    ->badge()
                    ->icon('heroicon-o-user')
                    ->label(__('user-credit.table.source_user_title'))
                    ->colors(function (UserCredit $record): array {
                        if ($record->sourceUser?->isActive()) {
                            return ['gray'];
                        }

                        return ['danger'];
                    }),
            ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Action::make('createNewNote')
                    ->model(UserCredit::class)
                    ->action(function (UserCredit $userCredit, array $data): void {
                        $userCreditNote = new UserCreditNote();
                        $userCreditNote->user_credit_id = $userCredit->id;
                        $userCreditNote->note_user_id = auth()->user()?->id ?? 1;
                        $userCreditNote->note = $data['user_note'];

                        if ($userCreditNote->save()) {
                            Notification::make()
                                ->title('Poznámku jsme uložili')
                                ->body('Děkujeme za zaslání dotazu k vyúčtování, pokusíme se to vyřešit.')
                                ->success()
                                ->seconds(8)
                                ->send();

                            $notificationUsers = User::role([AppRoles::BillingSpecialist->value, AppRoles::SuperAdmin->value])->get();
                            // vytahni si data podle $this->record
                            foreach ($notificationUsers as $recipient) {
                                Notification::make()
                                    ->title('Poznámka k vyúčtování')
                                    ->body('Uživatel: '.auth()->user()?->name.' | Vyúčtování ID: '.$userCredit->id
                                    .' zaslal zprávu. Prosíme o vyřešení poznámky.')
                                    ->sendToDatabase($recipient);
                            }
                        }
                    })
                    ->color('gray')
                    ->label('Info')
                    ->icon('heroicon-s-chat-bubble-oval-left-ellipsis')
                    ->modalHeading('Poznámka k platbě')
                    ->modalDescription('Pokud není něco v pořádků, sem prosím napište důvody proč je to jinak. Prosím stručně a věcně.')
                    ->modalSubmitActionLabel('Uložit poznámku')
                    ->modalContentFooter(fn (UserCredit $record): View => view(
                        'filament.modals.user-credit-comment',
                        ['record' => $record],
                    ))
                    ->form([
                        MarkdownEditor::make('user_note')
                            ->label('Poznámka'),
                    ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.user-credit.user-credit-list');
    }
}
