<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages;

use App\Enums\AppRoles;
use App\Enums\SportEventTransportType;
use App\Enums\TransportDirection;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Models\AppSetting;
use App\Models\SportEvent;
use App\Models\TransportOffer;
use App\Models\User;
use App\Models\Vehicle;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TransportSportEvent extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithRecord;
    use InteractsWithTable;

    public string|int|null|Model $record;

    protected static string $resource = SportEventResource::class;

    protected string $view = 'filament.resources.sport-event-resource.pages.event-transport';

    public function mount(string|int $record): void
    {
        $this->record = $this->resolveRecord($record);

        abort_unless(
            Auth::user()?->hasRole(User::ROLE_MEMBER.'|'.User::ROLE_EVENT_MASTER.'|'.User::ROLE_SUPER_ADMIN) === true,
            403
        );

        abort_unless($this->transportAvailable(), 404);
    }

    public function getTitle(): string
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        return __('transport.page_title').' - '.$sportEvent->name;
    }

    protected function getHeaderActions(): array
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        return [
            Action::make('back_to_entry')
                ->label('Zpět na přihlášky')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(SportEventResource::getUrl('entry', ['record' => $sportEvent])),
        ];
    }

    public function table(Table $table): Table
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        return $table
            ->query(
                TransportOffer::query()
                    ->forEvent($sportEvent->id)
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
                $this->offerCreateAction($sportEvent),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading(__('transport.edit_offer'))
                    ->schema($this->offerFormComponents()),
                DeleteAction::make(),
            ])
            ->emptyStateHeading(__('transport.empty_offers'))
            ->emptyStateDescription(__('transport.empty_offers_description'))
            ->emptyStateIcon('heroicon-o-truck')
            ->emptyStateActions([
                $this->offerCreateAction($sportEvent),
            ])
            ->paginated([10, 25, 50]);
    }

    private function offerCreateAction(SportEvent $sportEvent): CreateAction
    {
        return CreateAction::make()
            ->model(TransportOffer::class)
            ->authorize(fn (): bool => Auth::user()?->can('create', TransportOffer::class) ?? false)
            ->label(__('transport.offer_transport'))
            ->modalHeading(__('transport.offer_transport'))
            ->icon('heroicon-o-plus')
            ->visible(fn (): bool => $this->canOfferTransport())
            ->schema($this->offerFormComponents())
            ->mutateDataUsing(function (array $data) use ($sportEvent): array {
                $data['user_id'] = Auth::id();
                $data['sport_event_id'] = $sportEvent->id;

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
        return [
            Select::make('vehicle_id')
                ->label(__('transport.vehicle'))
                ->options($this->vehicleOptions())
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
     * Dostupná vozidla pro nabídku: vlastní aktivní, klubová jen pro
     * odpovídající typ dopravy a správcovské role.
     *
     * @return array<int, string>
     */
    private function vehicleOptions(): array
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        $options = Vehicle::query()
            ->ownedBy((int) Auth::id())
            ->active()
            ->orderBy('name')
            ->pluck('name', 'id');

        if ($sportEvent->transport_type->allowsClubVehicles() && $this->canManageClubTransport()) {
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

    private function canOfferTransport(): bool
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        if ($sportEvent->transport_type === SportEventTransportType::ClubOnly) {
            return $this->canManageClubTransport();
        }

        return true;
    }

    private function canManageClubTransport(): bool
    {
        return Auth::user()?->hasRole([AppRoles::SuperAdmin, AppRoles::ClubAdmin, AppRoles::EventMaster]) ?? false;
    }

    private function transportAvailable(): bool
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        return AppSetting::isTransportModuleEnabled()
            && $sportEvent->transport_type !== SportEventTransportType::None;
    }
}
