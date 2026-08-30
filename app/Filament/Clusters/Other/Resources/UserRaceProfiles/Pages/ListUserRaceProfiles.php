<?php

declare(strict_types=1);

namespace App\Filament\Clusters\Other\Resources\UserRaceProfiles\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\Action;
use App\Enums\AppRoles;
use App\Filament\Clusters\Other\Resources\UserRaceProfiles\UserRaceProfileResource;
use App\Services\OrisApiService;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListUserRaceProfiles extends ListRecords
{
    protected static string $resource = UserRaceProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->updateClubOrisId(),
        ];
    }

    protected function updateClubOrisId(): ?Action
    {
        return Action::make('updateClubOrisId')
            ->action(function (array $data): void {

                $result = (new OrisApiService())->updateUserClubId();

                if ($result) {

                    Notification::make()
                        ->title(__('user-race-profile.actions.update_club_oris_id.notification_success_title'))
                        ->body(__('user-race-profile.actions.update_club_oris_id.notification_success_body'))
                        ->success()
                        ->seconds(8)
                        ->send();
                } else {
                    Notification::make()
                        ->title(__('user-race-profile.actions.update_club_oris_id.notification_error_title'))
                        ->body(__('user-race-profile.actions.update_club_oris_id.notification_error_body'))
                        ->danger()
                        ->send();
                }
            })

            ->color('gray')
            ->label(__('user-race-profile.actions.update_club_oris_id.label'))
            ->icon('heroicon-m-arrow-path')
            ->modalHeading(__('user-race-profile.actions.update_club_oris_id.modal_heading'))
            ->modalDescription(__('user-race-profile.actions.update_club_oris_id.modal_description'))
            ->modalSubmitActionLabel(__('user-race-profile.actions.update_club_oris_id.modal_submit'))
            ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin->value]))
            ->schema([
            ]);
    }
}
