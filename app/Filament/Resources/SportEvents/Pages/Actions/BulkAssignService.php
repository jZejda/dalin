<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions;

use App\Enums\AppRoles;
use App\Models\AppSetting;
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

class BulkAssignService
{
    public static function make(SportEvent $sportEvent): BulkAction
    {
        return BulkAction::make('assignEventService')
            ->label(__('sport-event.actions.assign_service.label'))
            ->icon('heroicon-o-shopping-bag')
            ->color('primary')
            ->modalHeading(__('sport-event.actions.assign_service.modal_heading'))
            ->modalDescription(__('sport-event.actions.assign_service.modal_description'))
            ->modalSubmitActionLabel(__('sport-event.actions.assign_service.modal_submit'))
            ->visible(fn (): bool => self::isAllowed($sportEvent))
            ->schema([
                Select::make('sport_service_id')
                    ->label(__('sport-event.actions.assign_service.service'))
                    ->options(fn (): array => self::serviceOptions(
                        SportService::query()->where('sport_event_id', '=', $sportEvent->id)->get()
                    ))
                    ->required(),
                TextInput::make('qty')
                    ->label(__('sport-event.actions.assign_service.qty'))
                    ->integer()
                    ->minValue(1)
                    ->default(1)
                    ->required()
                    ->hintIcon('heroicon-m-question-mark-circle', tooltip: __('sport-event.actions.assign_service.qty_hint')),
                MarkdownEditor::make('note')
                    ->label(__('sport-event.actions.assign_service.note'))
                    ->hint(__('sport-event.actions.assign_service.note_hint')),
            ])
            ->action(function (Collection $records, array $data) use ($sportEvent): void {
                /** @var User $user */
                $user = Auth::user();

                $service = SportService::query()
                    ->where('sport_event_id', '=', $sportEvent->id)
                    ->findOrFail((int) $data['sport_service_id']);

                /** @var Collection<int, UserRaceProfile> $records */
                $created = (new ServiceCreditManager())->assign(
                    service: $service,
                    raceProfiles: $records,
                    qty: (int) $data['qty'],
                    note: isset($data['note']) ? (string) $data['note'] : null,
                    assignedBy: $user,
                );

                Notification::make()
                    ->title(__('sport-event.actions.assign_service.notification_title'))
                    ->body(__('sport-event.actions.assign_service.notification_body', ['count' => $created]))
                    ->success()
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }

    public static function isAllowed(SportEvent $sportEvent): bool
    {
        return AppSetting::isServiceOrdersModuleEnabled()
            && (Auth::user()?->hasRole([AppRoles::BillingSpecialist, AppRoles::SuperAdmin]) ?? false)
            && SportService::query()->where('sport_event_id', '=', $sportEvent->id)->exists();
    }

    /**
     * @param  iterable<SportService>  $services
     * @return array<int, string>
     */
    public static function serviceOptions(iterable $services): array
    {
        $options = [];

        foreach ($services as $service) {
            $options[$service->id] = __('sport-event.actions.assign_service.service_option', [
                'service' => $service->service_name_cz ?? ('#'.$service->id),
                'price' => number_format((float) $service->unit_price, 2, ',', ' '),
            ]);
        }

        return $options;
    }
}
