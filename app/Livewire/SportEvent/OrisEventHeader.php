<?php

declare(strict_types=1);

namespace App\Livewire\SportEvent;

use App\Enums\AppRoles;
use App\Models\SportEvent;
use App\Services\SportEvents\OrisEventStatusRefresher;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

/**
 * ORIS corner of the event entry page header: ORIS logo, start list / results links and — for the club's
 * finance roles — the entry-fee payment pill with a detail popover.
 */
class OrisEventHeader extends Component
{
    #[Locked]
    public SportEvent $sportEvent;

    /**
     * Roles that may see the club's money (settlement, organizer's account) and reload it from ORIS.
     */
    public static function canSeePayment(): bool
    {
        return Auth::user()?->hasRole([
            AppRoles::SuperAdmin->value,
            AppRoles::ClubAdmin->value,
            AppRoles::EventMaster->value,
            AppRoles::BillingSpecialist->value,
        ]) ?? false;
    }

    /**
     * Called once after the page has rendered (wire:init), so a slow ORIS never delays the page itself.
     */
    public function refreshIfStale(): void
    {
        (new OrisEventStatusRefresher())->refreshIfStale($this->sportEvent);
    }

    public function refreshFromOris(): void
    {
        if (! self::canSeePayment()) {
            return;
        }

        $refresher = new OrisEventStatusRefresher();
        $before = $this->sportEvent->orisStatus?->fetched_at;
        $status = $refresher->refreshQuietly($this->sportEvent);

        if ($status === null || ($before !== null && $status->fetched_at->equalTo($before))) {
            Notification::make()
                ->title(__('sport-event.oris_status.notifications.refresh_failed'))
                ->warning()
                ->send();
        }
    }

    public function render(): View
    {
        $status = $this->sportEvent->orisStatus;

        return view('livewire.sport-event.oris-event-header', [
            'status' => $status,
            'paymentState' => self::canSeePayment() ? $status?->paymentState() : null,
            'qrPayment' => self::canSeePayment() ? $status?->qrPayment() : null,
            // Demo statuses are seeded and never reloaded, see OrisEventStatusRefresher::refreshQuietly()
            'canRefresh' => self::canSeePayment() && ! config('demo.enabled'),
        ]);
    }
}
