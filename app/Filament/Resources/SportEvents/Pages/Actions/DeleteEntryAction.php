<?php

declare(strict_types=1);

namespace App\Filament\Resources\SportEvents\Pages\Actions;

use App\Enums\AppRoles;
use App\Enums\EntryStatus;
use App\Filament\Resources\SportEvents\Pages\Actions\Helpers\UserRaceProfiles;
use App\Models\UserEntry;
use App\Services\OrisApiService;
use App\Services\SportEvents\Entries\DeleteResult;
use App\Services\SportEvents\Entries\EntryDeleter;
use App\Shared\Helpers\AppHelper;
use Filament\Actions\Action as ActionAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class DeleteEntryAction
{
    public function make(): ActionAction
    {
        return ActionAction::make('delete_user_entry')
            ->hidden(fn (UserEntry $record): bool => $this->shouldHide($record))
            ->action(function (UserEntry $record): void {
                $profile = $record->userRaceProfile;
                $deletedRaceProfile = $profile instanceof \App\Models\UserRaceProfile ? $profile->user_race_full_name : '';
                $result = EntryDeleter::make()->delete($record);
                $this->sendNotification($result, $deletedRaceProfile);
            })
            ->color(fn (UserEntry $userEntry): string => Auth::user()?->id === $userEntry->userRaceProfile?->user?->id ? 'danger' : 'warning')
            ->label(__('sport-event.actions.delete_entry.label'))
            ->icon('heroicon-o-trash')
            ->disabled(fn (UserEntry $record): bool => $record->sportEvent instanceof \App\Models\SportEvent
                && AppHelper::allowModifyUserEntry($record->sportEvent)
                && ! AppHelper::allowModifyUserEntryAfterDeadline($record->sportEvent))
            ->modalHeading(function (UserEntry $record): string {
                $profile = $record->userRaceProfile;
                $name = $profile instanceof \App\Models\UserRaceProfile ? $profile->user_race_full_name : '';

                return __('sport-event.actions.delete_entry.modal_heading', ['profile' => $name]);
            })
            ->modalContent(view('filament.modals.user-cancel-entry'))
            ->modalDescription(__('sport-event.actions.delete_entry.modal_description'))
            ->modalSubmitActionLabel(__('sport-event.actions.delete_entry.modal_submit'));
    }

    public function shouldHide(UserEntry $userEntry): bool
    {
        if (Auth::user()?->hasRole([AppRoles::EventMaster, AppRoles::EventOrganizer])) {
            return false;
        }

        if ($userEntry->userRaceProfile !== null && UserRaceProfiles::allowAnotherUserCareTheirUserRaceProfile($userEntry->userRaceProfile)) {
            return false;
        }

        if ($userEntry->entry_status === EntryStatus::Cancel) {
            return true;
        }

        if ($userEntry->userRaceProfile?->user?->id !== Auth::user()?->id) {
            return true;
        }

        return false;
    }

    private function sendNotification(DeleteResult $result, string $profileName): void
    {
        if (! $result->success) {
            Notification::make()
                ->title(__('sport-event.actions.delete_entry.notification_title_failed'))
                ->body(__('sport-event.actions.delete_entry.notification_body_failed', ['error' => $result->orisStatusError]))
                ->warning()->send();

            return;
        }

        if ($result->wasOrisEntry) {
            Notification::make()
                ->title(__('sport-event.actions.delete_entry.notification_title_success', ['profile' => $profileName]))
                ->body(__('sport-event.actions.delete_entry.notification_body_oris'))
                ->actions([
                    ActionAction::make('view')
                        ->label(__('sport-event.actions.delete_entry.view_event_action'))
                        ->button()->openUrlInNewTab()
                        ->url(OrisApiService::ORIS_URL.'/Zavod?id='.$result->orisEventId),
                ])
                ->seconds(15)->send();

            return;
        }

        Notification::make()
            ->title(__('sport-event.actions.delete_entry.notification_title_success', ['profile' => $profileName]))
            ->body(__('sport-event.actions.delete_entry.notification_body_local'))
            ->warning()->send();
    }
}
