<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions;

use Filament\Actions\Action;
use Filament\Schemas\Components\Grid;
use App\Enums\AppRoles;
use App\Http\Controllers\UserEntryController;
use App\Models\SportEvent;
use Filament\Forms\Components\Select;
use Filament\Actions\Action as ModalAction;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Filament\Notifications\Notification;
use Livewire\Features\SupportRedirects\Redirector as LivewireRedirector;

class ExportsData
{
    private SportEvent $sportEvent;

    public function __construct(SportEvent $sportEvent)
    {
        $this->sportEvent = $sportEvent;
    }

    public function makeExport(): Action
    {
        return ModalAction::make('makeExport')
            ->action(function (array $data, ModalAction $action): Response|BinaryFileResponse|RedirectResponse|LivewireRedirector|null {
                /** @var SportEvent $sportEvent */

                if ($data['export_type'] === 'userEntryXlsx') {

                    Notification::make()
                        ->title(__('sport-event.actions.export.notification_title'))
                        ->body(__('sport-event.actions.export.notification_body_xlsx'))
                        ->success()
                        ->seconds(15)
                        ->send();

                    $action->close();
                    return (new UserEntryController())->exportXlsx($this->sportEvent->id);
                } elseif ($data['export_type'] === 'IofV3EntryList') {
                    Notification::make()
                        ->title(__('sport-event.actions.export.notification_title'))
                        ->body(__('sport-event.actions.export.notification_body_iof'))
                        ->success()
                        ->seconds(15)
                        ->send();

                    $action->close();
                    return response()->redirectTo(route('admin.export.event-entry-iof', ['eventId' => $this->sportEvent->id]));
                } elseif ($data['export_type'] === 'CSOS') {
                    Notification::make()
                        ->title(__('sport-event.actions.export.notification_title'))
                        ->body(__('sport-event.actions.export.notification_body_csos'))
                        ->success()
                        ->seconds(15)
                        ->send();

                    $action->close();
                    return response()->redirectTo(route('admin.export.event-entry-csos', ['eventId' => $this->sportEvent->id]));
                } else {
                    return null;
                }
            })
            ->color('gray')
            ->label(__('sport-event.actions.export.label'))
            ->icon('heroicon-o-document-arrow-down')
            ->modalHeading(__('sport-event.actions.export.modal_heading'))
            ->modalDescription(__('sport-event.actions.export.modal_description'))
            ->modalSubmitActionLabel(__('sport-event.actions.export.modal_submit'))
            ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin, AppRoles::EventMaster, AppRoles::EventOrganizer]))
            ->schema([
                Grid::make(1)
                    ->schema([
                        Select::make('export_type')
                            ->label(__('sport-event.actions.export.export_type'))
                            ->options([
                                'userEntryXlsx' => __('sport-event.actions.export.export_type_xlsx'),
                                'IofV3EntryList' => __('sport-event.actions.export.export_type_iof'),
                                'CSOS' => __('sport-event.actions.export.export_type_csos'),
                            ])
                            ->required(),
                    ]),

            ]);
    }
}
