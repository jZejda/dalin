<?php

namespace App\Livewire\UserCredit;

use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use App\Enums\AppRoles;
use App\Filament\Tables\Columns\PaymentCategoryColumn;
use App\Models\User;
use App\Models\UserCredit;
use App\Models\UserCreditNote;
use App\Shared\Helpers\AppHelper;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class UserCreditList extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public string $activeTab = '';

    public function table(Table $table): Table
    {
        return $table
            ->query(UserCredit::query()->with(['sportService'])->where('user_id', '=', auth()->user()?->id))
            ->columns([
                TextColumn::make('created_at')
                    ->label(__('user-credit.table.created_at_title'))
                    ->dateTime(AppHelper::DATE_FORMAT)
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
                    ->description(fn (UserCredit $record): string => $record->sportEvent?->alt_name !== null ? $record->sportEvent->alt_name : __('user-credit.table.no_event'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('userRaceProfile.reg_number')
                    ->label(__('user-credit.table.registration'))
                    ->html()
                    ->formatStateUsing(fn ($state, UserCredit $record): HtmlString => new HtmlString(
                        (string) view('components.race-profile-identity', [
                            'profile' => $record->userRaceProfile,
                            'size' => 'sm',
                        ])
                    ))
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
                    ->summarize(Sum::make())->money('CZK')->label(__('user-credit.table.amount_total')),
                PaymentCategoryColumn::make(),
                ViewColumn::make('user_entry')
                    ->label(__('user-credit.table.comments'))
                    ->view('filament.tables.columns.user-credit-comments-count'),
                ViewColumn::make('sourceUser.name')
                    ->label(__('user-credit.table.source_user_title'))
                    ->view('filament.tables.columns.user-badge'),
            ])
            ->defaultPaginationPageOption(25)
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('createNewNote')
                    ->model(UserCredit::class)
                    ->action(function (UserCredit $userCredit, array $data): void {
                        $userCreditNote = new UserCreditNote();
                        $userCreditNote->user_credit_id = $userCredit->id;
                        $userCreditNote->note_user_id = auth()->user()?->id ?? 1;
                        $userCreditNote->note = $data['user_note'];

                        if ($userCreditNote->save()) {
                            Notification::make()
                                ->title(__('user-credit.actions.credit_note.notification_title'))
                                ->body(__('user-credit.actions.credit_note.notification_body'))
                                ->success()
                                ->seconds(8)
                                ->send();

                            $notificationUsers = User::role([AppRoles::BillingSpecialist->value, AppRoles::SuperAdmin->value])->get();
                            foreach ($notificationUsers as $recipient) {
                                // Database notifications store the rendered text, so render it in the recipient's language
                                $locale = $recipient->preferredLocale();

                                Notification::make()
                                    ->title(__('user-credit.actions.credit_note.admin_notification_title', [], $locale))
                                    ->body(__('user-credit.actions.credit_note.admin_notification_body', [
                                        'user' => auth()->user()?->name,
                                        'id' => $userCredit->id,
                                    ], $locale))
                                    ->sendToDatabase($recipient);
                            }
                        }
                    })
                    ->color('gray')
                    ->label(__('user-credit.actions.credit_note.label'))
                    ->icon('heroicon-s-chat-bubble-oval-left-ellipsis')
                    ->modalHeading(__('user-credit.actions.credit_note.modal_heading'))
                    ->modalDescription(__('user-credit.actions.credit_note.modal_description'))
                    ->modalSubmitActionLabel(__('user-credit.actions.credit_note.modal_submit'))
                    ->modalContentFooter(fn (UserCredit $record): View => view(
                        'filament.modals.user-credit-comment',
                        ['record' => $record],
                    ))
                    ->schema([
                        MarkdownEditor::make('user_note')
                            ->label(__('user-credit.actions.credit_note.field_note')),
                    ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }

    public function render(): View
    {
        return view('livewire.user-credit.user-credit-list');
    }
}
