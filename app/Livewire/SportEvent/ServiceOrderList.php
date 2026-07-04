<?php

declare(strict_types=1);

namespace App\Livewire\SportEvent;

use App\Enums\AppRoles;
use App\Enums\ServiceOrderStatus;
use App\Models\SportEvent;
use App\Models\SportServiceOrder;
use App\Models\User;
use App\Services\SportEvents\Services\ServiceOrderBiller;
use App\Services\SportEvents\Services\ServiceOrderCanceller;
use App\Shared\Helpers\AppHelper;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ServiceOrderList extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    #[Locked]
    public SportEvent $sportEvent;

    #[On('service-order-changed')]
    public function refreshList(): void
    {
        // Re-render only, the table query picks up new orders.
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                SportServiceOrder::query()
                    ->where('sport_event_id', '=', $this->sportEvent->id)
                    ->with(['sportService', 'userRaceProfile', 'paymentDate', 'user'])
            )
            ->heading('Objednávky služeb')
            ->columns([
                TextColumn::make('sportService.service_name_cz')
                    ->label('Služba')
                    ->weight('medium')
                    ->searchable(),
                TextColumn::make('userRaceProfile.reg_number')
                    ->label('Závodní profil')
                    ->description(fn (SportServiceOrder $record): string => $record->userRaceProfile->user_race_full_name ?? '')
                    ->searchable(),
                TextColumn::make('qty')
                    ->label('Ks')
                    ->alignCenter(),
                TextColumn::make('unit_price')
                    ->label('Cena / ks')
                    ->money('CZK'),
                TextColumn::make('total_amount')
                    ->label('Celkem')
                    ->state(fn (SportServiceOrder $record): float => $record->totalAmount())
                    ->money('CZK')
                    ->weight('medium'),
                TextColumn::make('paymentDate.payment_date')
                    ->label('Zaplatit do')
                    ->date(AppHelper::DATE_FORMAT)
                    ->tooltip(fn (SportServiceOrder $record): ?string => $record->paymentDate?->description)
                    ->badge()
                    ->color(fn (SportServiceOrder $record): string => $record->paymentDate !== null && $record->paymentDate->payment_date->isPast()
                        ? 'danger'
                        : 'warning'),
                TextColumn::make('status')
                    ->label('Stav')
                    ->badge(),
                TextColumn::make('note')
                    ->label('Poznámka')
                    ->limit(30)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Objednáno')
                    ->dateTime(AppHelper::DATE_TIME_FORMAT)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Stav')
                    ->options(ServiceOrderStatus::enumArray())
                    ->multiple()
                    ->default([ServiceOrderStatus::Ordered->value, ServiceOrderStatus::Billed->value]),
            ])
            ->headerActions([
                $this->billAction(),
            ])
            ->recordActions([
                $this->cancelAction(),
            ])
            ->recordClasses('!py-0')
            ->defaultPaginationPageOption(50)
            ->defaultSort('created_at', 'desc');
    }

    public function render(): View
    {
        /** @var view-string $template */
        $template = 'livewire.sport-event.service-order-list';

        return view($template);
    }

    private function billAction(): Action
    {
        return Action::make('billServiceOrders')
            ->label('Vyúčtovat doplňkové služby')
            ->icon('heroicon-o-banknotes')
            ->color('primary')
            ->visible(fn (): bool => Auth::user()?->hasRole([AppRoles::BillingSpecialist, AppRoles::SuperAdmin]) ?? false)
            ->requiresConfirmation()
            ->modalHeading('Vyúčtovat doplňkové služby')
            ->modalDescription('Všem objednávkám ve stavu Objednáno se strhne částka z osobního konta a objednávky se označí jako vyúčtované. Už vyúčtované objednávky se přeskočí.')
            ->modalSubmitActionLabel('Vyúčtovat')
            ->action(function (): void {
                /** @var User|null $user */
                $user = Auth::user();
                if ($user === null) {
                    return;
                }

                $billed = (new ServiceOrderBiller())->billEvent($this->sportEvent, $user);

                Notification::make()
                    ->title('Doplňkové služby vyúčtovány')
                    ->body('Vyúčtováno objednávek: '.$billed)
                    ->success()->seconds(8)->send();

                $this->dispatch('service-order-changed');
            });
    }

    private function cancelAction(): Action
    {
        return Action::make('cancelOrder')
            ->label('Zrušit')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->visible(fn (SportServiceOrder $record): bool => $this->canCancel($record))
            ->requiresConfirmation()
            ->modalHeading('Zrušit objednávku služby')
            ->modalDescription(fn (SportServiceOrder $record): string => 'Objednávka '.($record->sportService->service_name_cz ?? '')
                .' pro '.($record->userRaceProfile->user_race_full_name ?? '').' bude zrušena'
                .($record->oris_service_entry_id !== null ? ' i v ORISu.' : '.'))
            ->action(function (SportServiceOrder $record): void {
                $result = (new ServiceOrderCanceller())->cancel($record);

                if ($result->success) {
                    Notification::make()
                        ->title('Objednávka zrušena')
                        ->success()->seconds(6)->send();

                    $this->dispatch('service-order-changed');
                } else {
                    Notification::make()
                        ->title('Objednávku se nepodařilo zrušit')
                        ->body($result->error ?? 'Neznámá chyba.')
                        ->danger()->seconds(10)->send();
                }
            });
    }

    private function canCancel(SportServiceOrder $record): bool
    {
        if ($record->status !== ServiceOrderStatus::Ordered) {
            return false;
        }

        $user = Auth::user();
        if ($user === null) {
            return false;
        }

        if ($user->hasRole([AppRoles::EventMaster, AppRoles::SuperAdmin, AppRoles::BillingSpecialist])) {
            return true;
        }

        $ownOrder = $record->user_id === $user->id || $record->source_user_id === $user->id;
        $bookingDeadline = $record->sportService?->last_booking_date_time;

        return $ownOrder
            && $bookingDeadline !== null
            && ! Carbon::parse($bookingDeadline)->isPast();
    }
}
