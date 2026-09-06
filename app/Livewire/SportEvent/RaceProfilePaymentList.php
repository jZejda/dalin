<?php

declare(strict_types=1);

namespace App\Livewire\SportEvent;

use App\Enums\EntryStatus;
use App\Filament\Resources\MemberFinances\MemberFinanceResource;
use App\Filament\Resources\SportEvents\Pages\Actions\BulkAssignPayment;
use App\Models\SportEvent;
use App\Models\UserRaceProfile;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;
use Livewire\Component;

class RaceProfilePaymentList extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    #[Locked]
    public SportEvent $sportEvent;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->baseQuery())
            ->groups([
                Group::make('user.name')
                    ->label('Uživatel')
                    ->collapsible(),
            ])
            ->defaultGroup('user.name')
            ->columns([
                TextColumn::make('reg_number')
                    ->label('Registrace')
                    ->html()
                    ->formatStateUsing(fn ($state, UserRaceProfile $record): HtmlString => new HtmlString(
                        (string) view('components.race-profile-identity', [
                            'profile' => $record,
                            'size' => 'sm',
                        ])
                    ))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('active_categories')
                    ->label('Kategorie')
                    ->html()
                    ->state(fn (UserRaceProfile $record): HtmlString => $this->renderCategories($record)),
                TextColumn::make('event_payment_sum')
                    ->label('Platby na závod')
                    ->money('CZK')
                    ->color(fn (?float $state): string => match (true) {
                        $state === null || $state === 0.0 => 'gray',
                        $state < 0 => 'danger',
                        default => 'success',
                    })
                    ->placeholder('— bez platby —')
                    ->sortable(),
                TextColumn::make('event_payment_count')
                    ->label('Počet plateb')
                    ->alignCenter()
                    ->badge()
                    ->color(fn (?int $state): string => ($state ?? 0) > 0 ? 'primary' : 'gray')
                    ->formatStateUsing(fn (?int $state): string => (string) ($state ?? 0))
                    ->sortable(),
                TextColumn::make('payment_status')
                    ->label('Stav platby')
                    ->badge()
                    ->state(fn (UserRaceProfile $record): string => ($record->event_payment_sum ?? 0.0) != 0.0 ? 'Má platbu' : 'Bez platby')
                    ->color(fn (UserRaceProfile $record): string => ($record->event_payment_sum ?? 0.0) != 0.0 ? 'success' : 'warning'),
                TextColumn::make('finance_link')
                    ->label('Vyúčtování')
                    ->state('Otevřít')
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (UserRaceProfile $record): string => MemberFinanceResource::getUrl('view', [
                        'record' => $record->user_id,
                        'sport_event_id' => $this->sportEvent->id,
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->filters([
                SelectFilter::make('payment_status')
                    ->label('Stav platby')
                    ->options([
                        'with' => 'Má platbu',
                        'without' => 'Bez platby',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $eventId = $this->sportEvent->id;

                        return match ($data['value'] ?? null) {
                            'with' => $query->whereHas(
                                'userCredits',
                                fn (Builder $q) => $q->where('sport_event_id', '=', $eventId)
                            ),
                            'without' => $query->whereDoesntHave(
                                'userCredits',
                                fn (Builder $q) => $q->where('sport_event_id', '=', $eventId)
                            ),
                            default => $query,
                        };
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAssignPayment::make($this->sportEvent),
                ]),
            ])
            ->recordClasses('!py-0')
            ->defaultPaginationPageOption(50)
            ->defaultSort('reg_number', 'asc');
    }

    public function render(): View
    {
        /** @var view-string $template */
        $template = 'livewire.sport-event.race-profile-payment-list';

        return view($template);
    }

    private function baseQuery(): Builder
    {
        $eventId = $this->sportEvent->id;

        return UserRaceProfile::query()
            ->whereHas('userEntries', function (Builder $q) use ($eventId): void {
                $q->where('sport_event_id', '=', $eventId)
                    ->where('entry_status', '!=', EntryStatus::Cancel->value);
            })
            ->with([
                'user',
                'userEntries' => function ($q) use ($eventId): void {
                    $q->where('sport_event_id', '=', $eventId)
                        ->where('entry_status', '!=', EntryStatus::Cancel->value);
                },
            ])
            ->withSum(
                [
                    'userCredits as event_payment_sum' => fn (Builder $q) => $q->where('sport_event_id', '=', $eventId),
                ],
                'amount',
            )
            ->withCount([
                'userCredits as event_payment_count' => fn (Builder $q) => $q->where('sport_event_id', '=', $eventId),
            ]);
    }

    private function renderCategories(UserRaceProfile $record): HtmlString
    {
        $categories = $record->userEntries
            ->pluck('class_name')
            ->filter()
            ->unique()
            ->values();

        if ($categories->isEmpty()) {
            return new HtmlString('<span class="text-xs text-gray-400">—</span>');
        }

        $badges = $categories
            ->map(fn (string $name): string => '<span class="inline-flex items-center rounded px-1.5 py-0.5 mr-1 text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">'.e($name).'</span>')
            ->implode('');

        return new HtmlString($badges);
    }
}
