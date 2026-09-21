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
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

/**
 * The current user's own carpool requests (passenger's view).
 */
class MyTransportRequestsList extends Component implements HasActions, HasForms, HasTable
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
            ->where('user_id', '=', Auth::id())
            ->whereHas('transportOffer', function (Builder $query): void {
                $query->where('sport_event_id', '=', $this->sportEvent->id);
            })
            ->with(['transportOffer.user', 'transportOffer.vehicle']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->requestsQuery())
            ->heading(__('transport.my_requests'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                ViewColumn::make('transportOffer.user.name')
                    ->label(__('transport.driver'))
                    ->view('filament.tables.columns.user-identity'),
                $this->tripColumn(showDeparture: true),
                $this->statusColumn(),
            ])
            ->recordActions([
                Action::make('cancelRequest')
                    ->label(__('transport.cancel_request'))
                    ->color('gray')
                    ->button()
                    ->visible(fn (TransportRequest $record): bool => $record->isPending() || $record->isApproved())
                    ->requiresConfirmation()
                    ->modalHeading(__('transport.cancel_request_modal_heading'))
                    ->modalDescription(__('transport.cancel_request_modal_description'))
                    ->modalSubmitActionLabel(__('transport.cancel_request_modal_submit'))
                    ->action(function (TransportRequest $record): void {
                        abort_unless($record->user_id === Auth::id(), 403);

                        app(TransportRequestService::class)->cancel($record);

                        Notification::make()->title(__('transport.request_cancelled_done'))->success()->send();

                        $this->dispatch('transport-requests-changed');
                    }),
            ])
            ->paginated(false);
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
