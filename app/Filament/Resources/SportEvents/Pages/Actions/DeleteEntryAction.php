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
            ->label('Odhlásit')
            ->icon('heroicon-o-trash')
            ->disabled(fn (UserEntry $record): bool => $record->sportEvent instanceof \App\Models\SportEvent && AppHelper::allowModifyUserEntry($record->sportEvent))
            ->modalHeading(function (UserEntry $record): string {
                $profile = $record->userRaceProfile;
                $name = $profile instanceof \App\Models\UserRaceProfile ? $profile->user_race_full_name : '';

                return $name.' - odhlášení ze závodu';
            })
            ->modalContent(view('filament.modals.user-cancel-entry'))
            ->modalDescription('Odhlášení proběhne pokud.')
            ->modalSubmitActionLabel('Odhlásit');
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
                ->title('Něco se nepovedlo')
                ->body('Toto pošli správci: '.$result->orisStatusError)
                ->warning()->send();

            return;
        }

        if ($result->wasOrisEntry) {
            Notification::make()
                ->title('Úspěšně jsme odhlásili '.$profileName.' ze závodu')
                ->body('Odhlášku doporučujeme zkontrolovat na ORISu.')
                ->actions([
                    ActionAction::make('view')
                        ->label('Přejít na url závodu')
                        ->button()->openUrlInNewTab()
                        ->url(OrisApiService::ORIS_URL.'/Zavod?id='.$result->orisEventId),
                ])
                ->seconds(15)->send();

            return;
        }

        Notification::make()
            ->title('Úspěšně jsme odhlásili '.$profileName.' ze závodu')
            ->body('Odhlášku proběhlo pouze v našem systému.')
            ->warning()->send();
    }
}
