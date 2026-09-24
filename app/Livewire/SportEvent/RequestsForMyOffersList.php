<?php

declare(strict_types=1);

namespace App\Livewire\SportEvent;

use App\Livewire\SportEvent\Concerns\HasTransportRequestColumns;
use App\Models\SportEvent;
use App\Models\TransportRequest;
use App\Services\TransportRequestService;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * Requests for seats in the current user's offers (driver's view).
 */
class RequestsForMyOffersList extends Component implements HasActions, HasForms, HasTable
{
    use HasTransportRequestColumns;
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    #[Locked]
    public SportEvent $sportEvent;

    /**
     * @return Builder<TransportRequest>
     */
    private function requestsQuery(): Builder
    {
        return TransportRequest::query()
            ->whereHas('transportOffer', function (Builder $query): void {
                $query->where('sport_event_id', '=', $this->sportEvent->id)
                    ->where('user_id', '=', Auth::id());
            })
            ->with(['user', 'transportOffer.vehicle']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->requestsQuery())
            ->heading(__('transport.requests_for_my_offers'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                ViewColumn::make('user.name')
                    ->label(__('transport.user'))
                    ->view('filament.tables.columns.user-identity'),
                $this->tripColumn(showDeparture: false),
                $this->statusColumn(),
            ])
            ->recordActions([
                Action::make('approve')
                    ->authorize(fn (TransportRequest $record): bool => $record->transportOffer?->user_id === Auth::id())
                    ->label(__('transport.approve'))
                    ->color('success')
                    ->button()
                    ->visible(fn (TransportRequest $record): bool => $record->isPending())
                    ->action(function (TransportRequest $record): void {
                        $this->authorizeDriver($record);

                        if (app(TransportRequestService::class)->approve($record)) {
                            Notification::make()->title(__('transport.request_approved'))->success()->send();
                        } else {
                            Notification::make()->title(__('transport.request_rejected_capacity'))->danger()->send();
                        }

                        $this->dispatch('transport-requests-changed');
                    }),
                Action::make('reject')
                    ->authorize(fn (TransportRequest $record): bool => $record->transportOffer?->user_id === Auth::id())
                    ->label(__('transport.reject'))
                    ->color('danger')
                    ->button()
                    ->visible(fn (TransportRequest $record): bool => $record->isPending())
                    ->action(function (TransportRequest $record): void {
                        $this->authorizeDriver($record);

                        app(TransportRequestService::class)->reject($record);

                        Notification::make()->title(__('transport.request_rejected_done'))->success()->send();

                        $this->dispatch('transport-requests-changed');
                    }),
            ])
            ->paginated(false);
    }

    private function authorizeDriver(TransportRequest $transportRequest): void
    {
        abort_unless($transportRequest->transportOffer?->user_id === Auth::id(), 403);
    }

    /**
     * Re-renders the table when requests or offers change elsewhere on the page.
     */
    #[On('transport-requests-changed')]
    public function refreshRequests(): void
    {
    }

    public function render(): View
    {
        /** @var view-string $template */
        $template = 'livewire.sport-event.transport-requests-table';

        return view($template, ['hasRecords' => $this->requestsQuery()->exists()]);
    }
}
