<?php

declare(strict_types=1);

namespace App\Livewire\SportEvent;

use App\Enums\AppRoles;
use App\Models\SportEvent;
use App\Models\SportService;
use App\Models\SportServicePaymentDate;
use App\Models\User;
use App\Models\UserRaceProfile;
use App\Services\SportEvents\Services\ServiceOrderCreator;
use App\Shared\Helpers\AppHelper;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ServiceList extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    #[Locked]
    public SportEvent $sportEvent;

    #[On('service-order-changed')]
    public function refreshList(): void
    {
        // Re-render only, the table query picks up updated capacities.
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(SportService::query()->where('sport_event_id', '=', $this->sportEvent->id))
            ->heading('Nabízené služby')
            ->columns([
                TextColumn::make('service_name_cz')
                    ->label('Služba')
                    ->weight('medium')
                    ->searchable(),
                TextColumn::make('unit_price')
                    ->label('Cena / ks')
                    ->money('CZK')
                    ->sortable(),
                TextColumn::make('last_booking_date_time')
                    ->label('Objednat do')
                    ->dateTime(AppHelper::DATE_TIME_FORMAT)
                    ->color(fn (SportService $record): string => Carbon::parse($record->last_booking_date_time)->isPast() ? 'danger' : 'success')
                    ->sortable(),
                TextColumn::make('qty_remaining')
                    ->label('Volná kapacita')
                    ->alignCenter()
                    ->state(fn (SportService $record): string => ($record->qty_available ?? 0) > 0
                        ? ($record->qty_remaining ?? 0).' / '.$record->qty_available
                        : 'neomezeno')
                    ->badge()
                    ->color(fn (SportService $record): string => ($record->qty_available ?? 0) > 0 && ($record->qty_remaining ?? 0) === 0 ? 'danger' : 'gray'),
                TextColumn::make('paymentDates.payment_date')
                    ->label('Termíny plateb')
                    ->state(fn (SportService $record): array => $record->paymentDates
                        ->sortBy('payment_date')
                        ->map(fn (SportServicePaymentDate $date): string => $date->payment_date->format(AppHelper::DATE_FORMAT).' | '.$date->description)
                        ->values()
                        ->all())
                    ->listWithLineBreaks()
                    ->badge()
                    ->color('warning')
                    ->placeholder('— zatím bez termínu —'),
            ])
            ->recordActions([
                $this->addPaymentDateAction(),
                $this->orderAction(),
            ])
            ->paginated(false)
            ->defaultSort('service_name_cz', 'asc');
    }

    public function render(): View
    {
        /** @var view-string $template */
        $template = 'livewire.sport-event.service-list';

        return view($template);
    }

    private function orderAction(): Action
    {
        return Action::make('orderService')
            ->label('Objednat')
            ->icon('heroicon-o-shopping-cart')
            ->color('primary')
            ->button()
            ->visible(fn (): bool => ! $this->sportEvent->cancelled)
            ->disabled(fn (SportService $record): bool => Carbon::parse($record->last_booking_date_time)->isPast()
                || ! (Auth::user()?->canCreateEntry() ?? false))
            ->modalHeading(fn (SportService $record): string => 'Objednat službu: '.$record->service_name_cz)
            ->modalDescription('Vyber závodní profil a termín platby. Bez termínu platby nelze službu objednat.')
            ->modalSubmitActionLabel('Objednat')
            ->schema(fn (SportService $record): array => [
                Select::make('raceProfileId')
                    ->label('Závodní profil')
                    ->options($this->raceProfileOptions())
                    ->searchable()
                    ->required(),
                TextInput::make('qty')
                    ->label('Počet kusů')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->required(),
                Select::make('paymentDateId')
                    ->label('Termín platby')
                    ->options(fn (): array => $this->paymentDateOptions($record))
                    ->required()
                    ->hint('Pokud termín chybí, přidej nový.')
                    ->createOptionForm($this->paymentDateFormSchema())
                    ->createOptionModalHeading('Přidat nový termín platby')
                    ->createOptionUsing(fn (array $data): int => $this->createPaymentDate($record, $data)->id),
                TextInput::make('note')
                    ->label('Poznámka')
                    ->hint('Nepovinná, odešle se i do ORISu.'),
            ])
            ->action(function (SportService $record, array $data): void {
                $raceProfile = UserRaceProfile::query()->find((int) $data['raceProfileId']);
                $paymentDate = SportServicePaymentDate::query()->find((int) $data['paymentDateId']);
                /** @var User|null $sourceUser */
                $sourceUser = Auth::user();

                if ($raceProfile === null || $paymentDate === null || $sourceUser === null) {
                    Notification::make()
                        ->title('Objednávku se nepodařilo vytvořit')
                        ->body('Vybraný profil nebo termín platby neexistuje.')
                        ->danger()->seconds(8)->send();

                    return;
                }

                $result = (new ServiceOrderCreator())->create(
                    service: $record,
                    raceProfile: $raceProfile,
                    paymentDate: $paymentDate,
                    qty: (int) $data['qty'],
                    note: $data['note'] ?? null,
                    sourceUser: $sourceUser,
                );

                if ($result->success) {
                    Notification::make()
                        ->title('Služba objednána')
                        ->body($record->service_name_cz.' pro '.$raceProfile->user_race_full_name.' ('.$result->order?->qty.' ks).')
                        ->success()->seconds(8)->send();

                    $this->dispatch('service-order-changed');
                } else {
                    Notification::make()
                        ->title('Objednávku se nepodařilo vytvořit')
                        ->body($result->error ?? 'Neznámá chyba.')
                        ->danger()->seconds(10)->send();
                }
            });
    }

    private function addPaymentDateAction(): Action
    {
        return Action::make('addPaymentDate')
            ->label('Přidat termín')
            ->icon('heroicon-o-calendar-days')
            ->color('gray')
            ->modalHeading(fn (SportService $record): string => 'Termín platby pro: '.$record->service_name_cz)
            ->modalDescription('Uveď do kdy se má služba zaplatit a odkud tuto informaci máš (rozpis, e-mail pořadatele apod.).')
            ->modalSubmitActionLabel('Uložit termín')
            ->schema($this->paymentDateFormSchema())
            ->action(function (SportService $record, array $data): void {
                $this->createPaymentDate($record, $data);

                Notification::make()
                    ->title('Termín platby přidán')
                    ->success()->seconds(6)->send();
            });
    }

    /** @return list<mixed> */
    private function paymentDateFormSchema(): array
    {
        return [
            DatePicker::make('payment_date')
                ->label('Zaplatit do')
                ->native(false)
                ->displayFormat(AppHelper::DATE_FORMAT)
                ->required(),
            TextInput::make('description')
                ->label('Zdroj informace')
                ->hint('Např. „rozpis závodu, kap. 5" nebo „e-mail pořadatele 1. 7.".')
                ->required()
                ->maxLength(255),
        ];
    }

    /** @param array<string, mixed> $data */
    private function createPaymentDate(SportService $service, array $data): SportServicePaymentDate
    {
        $paymentDate = new SportServicePaymentDate();
        $paymentDate->sport_service_id = $service->id;
        $paymentDate->payment_date = Carbon::parse((string) $data['payment_date']);
        $paymentDate->description = (string) $data['description'];
        $paymentDate->created_by_user_id = (int) Auth::id();
        $paymentDate->saveOrFail();

        return $paymentDate;
    }

    /** @return array<int, string> */
    private function raceProfileOptions(): array
    {
        $orderForAnyone = Auth::user()?->hasRole([AppRoles::EventMaster, AppRoles::EventOrganizer, AppRoles::SuperAdmin]) ?? false;

        $query = UserRaceProfile::query()
            ->where('active', '=', '1')
            ->orderBy('reg_number');

        if (! $orderForAnyone) {
            $query->where('user_id', '=', Auth::id());
        }

        return $query->get()
            ->mapWithKeys(fn (UserRaceProfile $profile): array => [$profile->id => $profile->user_race_full_name])
            ->all();
    }

    /** @return array<int, string> */
    private function paymentDateOptions(SportService $service): array
    {
        return SportServicePaymentDate::query()
            ->where('sport_service_id', '=', $service->id)
            ->orderBy('payment_date')
            ->get()
            ->mapWithKeys(fn (SportServicePaymentDate $date): array => [
                $date->id => $date->payment_date->format(AppHelper::DATE_FORMAT).' — '.$date->description,
            ])
            ->all();
    }
}
