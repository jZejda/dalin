<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages;

use App\Enums\AppRoles;
use App\Enums\SportEventTransportType;
use App\Filament\Resources\SportEvents\Pages\Actions\CreateEntryAction;
use App\Filament\Resources\SportEvents\Pages\Actions\EntrySendMail;
use App\Filament\Resources\SportEvents\Pages\Actions\EntryUpdateEvent;
use App\Filament\Resources\SportEvents\Pages\Actions\ExportsData;
use App\Filament\Resources\SportEvents\SportEventResource;
use App\Models\AppSetting;
use App\Models\SportEvent;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class EntrySportEvent extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithRecord;

    public string|int|null|Model $record;

    protected static string $resource = SportEventResource::class;

    protected string $view = 'filament.resources.sport-event-resource.pages.event-entry';

    public string $back_button_url = '/admin/sport-events';

    public function booted(): void
    {
        // @todo refactor check Filament::user ability.
        if (! Auth::user()?->hasRole(User::ROLE_MEMBER.'|'.User::ROLE_EVENT_MASTER.'|'.User::ROLE_SUPER_ADMIN.'|'.AppRoles::BillingSpecialist->value)) {
            $this->notify('warning', __('filament-shield::filament-shield.forbidden'));
            $this->beforeShieldRedirects();
            redirect($this->getShieldRedirectPath());

            return;
        }

        if (method_exists(parent::class, 'booted')) {
            parent::booted();
        }
    }

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
    }

    protected array $rules = [
        'oris_class_id' => 'required|min:3',
    ];

    public function getTitle(): string
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        return 'Detail závodu - '.$sportEvent->name;
    }

    protected function getHeaderActions(): array
    {
        /** @var SportEvent $sportEvent */
        $sportEvent = $this->record;

        $createEntry = new CreateEntryAction($sportEvent);
        $updateEvent = new EntryUpdateEvent($sportEvent);
        $sendMailModal = new EntrySendMail($sportEvent);
        $makeExportModal = new ExportsData($sportEvent);

        $registerAnyone = Auth::user()?->hasRole([AppRoles::EventMaster, AppRoles::EventOrganizer])
            ? $createEntry->register(registerAll: true)
            : null;
        $sendEmail = Auth::user()?->hasRole([AppRoles::EventMaster, AppRoles::SuperAdmin, AppRoles::EventOrganizer])
            ? $sendMailModal->sendNotification()
            : null;
        $makeExport = Auth::user()?->hasRole([AppRoles::EventMaster, AppRoles::SuperAdmin, AppRoles::EventOrganizer])
            ? $makeExportModal->makeExport()
            : null;

        $defaultActions = [$createEntry->register()];

        if (! is_null($sportEvent->oris_id)) {
            $defaultActions[] = $updateEvent->showUpdateEventFromOris();
        }
        if (! is_null($registerAnyone)) {
            $defaultActions[] = $registerAnyone;
        }
        if (! is_null($sendEmail)) {
            $defaultActions[] = $sendEmail;
        }
        if (! is_null($makeExport)) {
            $defaultActions[] = $makeExport;
        }

        if (AppSetting::isTransportModuleEnabled()
            && $sportEvent->transport_type !== SportEventTransportType::None
        ) {
            $defaultActions[] = Action::make('transport')
                ->label(__('transport.page_title'))
                ->icon('heroicon-o-truck')
                ->color('gray')
                ->url(SportEventResource::getUrl('transport', ['record' => $sportEvent]));
        }

        return $defaultActions;
    }

    public function getHeaderWidgetsColumns(): int|array
    {
        return 3;
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
