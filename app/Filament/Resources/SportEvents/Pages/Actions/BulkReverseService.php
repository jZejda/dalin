<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions;

use App\Models\SportEvent;
use App\Models\SportService;
use App\Models\User;
use App\Models\UserRaceProfile;
use App\Services\SportEvents\Services\ServiceCreditManager;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class BulkReverseService
{
    public static function make(SportEvent $sportEvent): BulkAction
    {
        return BulkAction::make('reverseEventService')
            ->label(__('sport-event.actions.reverse_service.label'))
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('danger')
            ->modalHeading(__('sport-event.actions.reverse_service.modal_heading'))
            ->modalDescription(__('sport-event.actions.reverse_service.modal_description'))
            ->modalSubmitActionLabel(__('sport-event.actions.reverse_service.modal_submit'))
            ->visible(fn (): bool => BulkAssignService::isAllowed($sportEvent))
            ->schema(fn (Collection $records): array => [
                // Only services that at least one selected profile is currently charged for.
                Select::make('sport_service_id')
                    ->label(__('sport-event.actions.reverse_service.service'))
                    ->options(BulkAssignService::serviceOptions(
                        SportService::query()
                            ->whereKey((new ServiceCreditManager())->chargedServiceIds(
                                $sportEvent->id,
                                array_map(intval(...), $records->modelKeys()),
                            ))
                            ->get()
                    ))
                    ->helperText(__('sport-event.actions.reverse_service.service_hint'))
                    ->required(),
                TextInput::make('qty')
                    ->label(__('sport-event.actions.reverse_service.qty'))
                    ->integer()
                    ->minValue(1)
                    ->default(1)
                    ->required()
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('sport-event.actions.reverse_service.qty_hint')),
                MarkdownEditor::make('note')
                    ->label(__('sport-event.actions.reverse_service.note'))
                    ->hint(__('sport-event.actions.reverse_service.note_hint')),
            ])
            ->action(function (Collection $records, array $data) use ($sportEvent): void {
                /** @var User $user */
                $user = Auth::user();

                $service = SportService::query()
                    ->where('sport_event_id', '=', $sportEvent->id)
                    ->findOrFail((int) $data['sport_service_id']);

                /** @var Collection<int, UserRaceProfile> $records */
                $reversed = (new ServiceCreditManager())->reverse(
                    service: $service,
                    raceProfiles: $records,
                    qty: (int) $data['qty'],
                    note: isset($data['note']) ? (string) $data['note'] : null,
                    reversedBy: $user,
                );

                Notification::make()
                    ->title(__('sport-event.actions.reverse_service.notification_title'))
                    ->body(__('sport-event.actions.reverse_service.notification_body', [
                        'count' => $reversed,
                        'skipped' => $records->count() - $reversed,
                    ]))
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}
