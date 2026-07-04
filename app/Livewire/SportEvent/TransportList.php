<?php

declare(strict_types=1);

namespace App\Livewire\SportEvent;

use App\Enums\AppRoles;
use App\Enums\SportEventTransportType;
use App\Enums\TransportDirection;
use App\Models\SportEvent;
use App\Models\TransportOffer;
use App\Models\TransportRequest;
use App\Models\User;
use App\Models\Vehicle;
use App\Services\TransportRequestService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class TransportList extends Component implements HasActions, HasForms, HasTable
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    #[Locked]
    public SportEvent $sportEvent;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TransportOffer::query()
                    ->forEvent($this->sportEvent->id)
                    ->with(['user', 'vehicle', 'requests'])
                    ->where(function (Builder $query): void {
                        $query->where('active', '=', true)
                            ->orWhere('user_id', '=', Auth::id());
                    })
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label(__('transport.driver'))
                    ->searchable(),
                TextColumn::make('vehicle.name')
                    ->label(__('transport.vehicle'))
                    ->formatStateUsing(fn (string $state, TransportOffer $record): string => $record->vehicle?->isClubVehicle() === true
                        ? __('transport.club_vehicle_prefix').' '.$state
                        : $state),
                TextColumn::make('departure_place')
                    ->label(__('transport.departure_place'))
                    ->searchable(),
                TextColumn::make('direction')
                    ->label(__('transport.direction'))
                    ->badge()
                    ->formatStateUsing(fn (TransportDirection $state): string => $state->label())
                    ->color('info'),
                TextColumn::make('seats_offered')
                    ->label(__('transport.seats_offered')),
                TextColumn::make('free_seats')
                    ->label(__('transport.free_seats'))
                    ->state(fn (TransportOffer $record): int => $record->freeSeats())
                    ->badge()
                    ->color(fn (int $state): string => $state > 0 ? 'success' : 'danger'),
                TextColumn::make('distance_km')
                    ->label(__('transport.distance_km'))
                    ->suffix(' '.__('transport.distance_km_suffix'))
                    ->placeholder('—'),
                TextColumn::make('contribution')
                    ->label(__('transport.contribution'))
                    ->suffix(' '.__('transport.contribution_suffix'))
                    ->placeholder(__('transport.without_contribution')),
                IconColumn::make('active')
                    ->label(__('transport.active'))
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                $this->offerCreateAction(),
            ])
            ->recordActions([
                Action::make('requestSeat')
                    ->label(__('transport.request_seat'))
                    ->icon('heroicon-o-hand-raised')
                    ->color('success')
                    ->button()
                    ->visible(fn (TransportOffer $record): bool => $record->user_id !== Auth::id()
                        && $record->active
                        && $record->maxRequestableSeats() > 0)
                    ->schema(fn (TransportOffer $record): array => [
                        Select::make('direction')
                            ->label(__('transport.direction'))
                            ->options($this->directionOptionsFor($record))
                            ->default($record->direction->value)
                            ->required(),
                        TextInput::make('seats')
                            ->label(__('transport.seats'))
                            ->numeric()
                            ->minValue(1)
                            ->maxValue($record->maxRequestableSeats())
                            ->default(1)
                            ->required(),
                    ])
                    ->modalHeading(__('transport.request_seat'))
                    ->action(function (TransportOffer $record, array $data): void {
                        /** @var User $user */
                        $user = Auth::user();

                        app(TransportRequestService::class)->create(
                            $record,
                            $user,
                            TransportDirection::from((string) $data['direction']),
                            (int) $data['seats'],
                        );

                        Notification::make()
                            ->title(__('transport.request_sent'))
                            ->body(__('transport.request_sent_body'))
                            ->success()
                            ->send();
                    }),
                EditAction::make()
                    ->modalHeading(__('transport.edit_offer'))
                    ->schema($this->offerFormComponents()),
                DeleteAction::make()
                    ->after(fn (TransportOffer $record) => app(TransportRequestService::class)->cancelOffer($record)),
            ])
            ->emptyStateHeading(__('transport.empty_offers'))
            ->emptyStateDescription(__('transport.empty_offers_description'))
            ->emptyStateIcon('heroicon-o-truck')
            ->emptyStateActions([
                $this->offerCreateAction(),
            ])
            ->paginated([10, 25, 50]);
    }

    private function offerCreateAction(): CreateAction
    {
        return CreateAction::make()
            ->model(TransportOffer::class)
            ->authorize(fn (): bool => Auth::user()?->can('create', TransportOffer::class) ?? false)
            ->label(__('transport.offer_transport'))
            ->modalHeading(__('transport.offer_transport'))
            ->icon('heroicon-o-plus')
            ->visible(fn (): bool => $this->canOfferTransport())
            ->schema($this->offerFormComponents())
            ->mutateDataUsing(function (array $data): array {
                $data['user_id'] = Auth::id();
                $data['sport_event_id'] = $this->sportEvent->id;

                return $data;
            });
    }

    /**
     * Formulář nabídky dopravy.
     *
     * @return array<int, \Filament\Forms\Components\Field>
     */
    private function offerFormComponents(): array
    {
        $defaultVehicle = $this->defaultVehicle();

        return [
            Select::make('vehicle_id')
                ->label(__('transport.vehicle'))
                ->options($this->vehicleOptions())
                ->default($defaultVehicle?->id)
                ->required()
                ->live()
                ->afterStateUpdated(function (Set $set, ?string $state): void {
                    $vehicle = Vehicle::query()->find((int) $state);
                    if ($vehicle !== null) {
                        $set('seats_offered', $vehicle->seats);
                    }
                }),
            TextInput::make('departure_place')
                ->label(__('transport.departure_place'))
                ->required()
                ->maxLength(255),
            Select::make('direction')
                ->label(__('transport.direction'))
                ->options(TransportDirection::enumArray())
                ->default(TransportDirection::Both->value)
                ->required(),
            TextInput::make('seats_offered')
                ->label(__('transport.seats_offered'))
                ->numeric()
                ->minValue(1)
                ->maxValue(100)
                ->default($defaultVehicle?->seats)
                ->required(),
            TextInput::make('distance_km')
                ->label(__('transport.distance_km'))
                ->numeric()
                ->minValue(1)
                ->suffix(__('transport.distance_km_suffix')),
            TextInput::make('contribution')
                ->label(__('transport.contribution'))
                ->numeric()
                ->minValue(0)
                ->suffix(__('transport.contribution_suffix'))
                ->helperText(__('transport.contribution_helper')),
        ];
    }

    /**
     * Výchozí vozidlo přihlášeného uživatele, kterým se předvyplní nabídka.
     */
    private function defaultVehicle(): ?Vehicle
    {
        return Vehicle::query()
            ->ownedBy((int) Auth::id())
            ->active()
            ->default()
            ->first();
    }

    /**
     * Dostupná vozidla pro nabídku: vlastní aktivní, klubová jen pro
     * odpovídající typ dopravy a správcovské role.
     *
     * @return array<int, string>
     */
    private function vehicleOptions(): array
    {
        $options = Vehicle::query()
            ->ownedBy((int) Auth::id())
            ->active()
            ->orderBy('name')
            ->pluck('name', 'id');

        if ($this->sportEvent->transport_type->allowsClubVehicles() && $this->canManageClubTransport()) {
            $clubOptions = Vehicle::query()
                ->club()
                ->active()
                ->orderBy('name')
                ->pluck('name', 'id')
                ->map(fn (string $name): string => __('transport.club_vehicle_prefix').' '.$name);

            $options = $options->union($clubOptions);
        }

        return $options->all();
    }

    /**
     * Směry, o které lze v dané nabídce požádat.
     *
     * @return array<string, string>
     */
    private function directionOptionsFor(TransportOffer $offer): array
    {
        return array_filter(
            TransportDirection::enumArray(),
            fn (string $value): bool => $offer->direction->overlaps(TransportDirection::from($value))
                && ($offer->direction === TransportDirection::Both || TransportDirection::from($value) !== TransportDirection::Both),
            ARRAY_FILTER_USE_KEY,
        );
    }

    /**
     * Žádosti o místa v nabídkách přihlášeného uživatele (pohled řidiče).
     *
     * @return Collection<int, TransportRequest>
     */
    public function getRequestsForMyOffersProperty(): Collection
    {
        return TransportRequest::query()
            ->whereHas('transportOffer', function (Builder $query): void {
                $query->where('sport_event_id', '=', $this->sportEvent->id)
                    ->where('user_id', '=', Auth::id());
            })
            ->with(['user', 'transportOffer.vehicle'])
            ->orderByDesc('created_at')
            ->get();
    }

    /**
     * Žádosti přihlášeného uživatele (pohled spolujezdce).
     *
     * @return Collection<int, TransportRequest>
     */
    public function getMyRequestsProperty(): Collection
    {
        return TransportRequest::query()
            ->where('user_id', '=', Auth::id())
            ->whereHas('transportOffer', function (Builder $query): void {
                $query->where('sport_event_id', '=', $this->sportEvent->id);
            })
            ->with(['transportOffer.user', 'transportOffer.vehicle'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function approveRequest(int $requestId): void
    {
        $transportRequest = TransportRequest::query()->findOrFail($requestId);
        abort_unless($transportRequest->transportOffer?->user_id === Auth::id(), 403);

        $approved = app(TransportRequestService::class)->approve($transportRequest);

        if ($approved) {
            Notification::make()->title(__('transport.request_approved'))->success()->send();
        } else {
            Notification::make()->title(__('transport.request_rejected_capacity'))->danger()->send();
        }
    }

    public function rejectRequest(int $requestId): void
    {
        $transportRequest = TransportRequest::query()->findOrFail($requestId);
        abort_unless($transportRequest->transportOffer?->user_id === Auth::id(), 403);

        app(TransportRequestService::class)->reject($transportRequest);

        Notification::make()->title(__('transport.request_rejected_done'))->success()->send();
    }

    public function cancelRequest(int $requestId): void
    {
        $transportRequest = TransportRequest::query()->findOrFail($requestId);
        abort_unless($transportRequest->user_id === Auth::id(), 403);

        app(TransportRequestService::class)->cancel($transportRequest);

        Notification::make()->title(__('transport.request_cancelled_done'))->success()->send();
    }

    private function canOfferTransport(): bool
    {
        if ($this->sportEvent->transport_type === SportEventTransportType::ClubOnly) {
            return $this->canManageClubTransport();
        }

        return true;
    }

    private function canManageClubTransport(): bool
    {
        return Auth::user()?->hasRole([AppRoles::SuperAdmin, AppRoles::ClubAdmin, AppRoles::EventMaster]) ?? false;
    }

    public function render(): View
    {
        /** @var view-string $template */
        $template = 'livewire.sport-event.transport-list';

        return view($template);
    }
}
