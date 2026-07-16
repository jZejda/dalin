<?php

declare(strict_types=1);

namespace App\Filament\Pages\Actions;

use Filament\Schemas\Components\Grid;
use App\Enums\AppRoles;
use App\Http\Controllers\UserRaceProfileController;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Illuminate\Http\Response;
use Illuminate\Support\HtmlString;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

final class ExportUserRaceProfileData
{
    public const string ALL_REGISTRATIONS = 'userRaceProfileAll';
    public const string ACTIVE_REGISTRATIONS = 'userRaceProfileActive';
    public const string DEACTIVATED_REGISTRATIONS = 'userRaceProfileDeactivated';

    public static function makeExport(): Action
    {
        return Action::make('makeExportUserRaceProfile')
            ->action(function (array $data): Response|BinaryFileResponse|null {
                if ($data['export_type'] === self::ALL_REGISTRATIONS) {
                    self::getNotificationMessage();
                    return (new UserRaceProfileController())->export(self::ALL_REGISTRATIONS);
                } elseif ($data['export_type'] === self::ACTIVE_REGISTRATIONS) {
                    self::getNotificationMessage();
                    return (new UserRaceProfileController())->export(self::ACTIVE_REGISTRATIONS);
                } elseif ($data['export_type'] === self::DEACTIVATED_REGISTRATIONS) {
                    self::getNotificationMessage();
                    return (new UserRaceProfileController())->export(self::DEACTIVATED_REGISTRATIONS);
                } else {
                    return null;
                }
            })
            ->color('gray')
            ->label(__('user-race-profile.actions.export.label'))
            ->icon('heroicon-o-document-arrow-down')
            ->modalHeading(__('user-race-profile.actions.export.modal_heading'))
            ->modalDescription(function (): HtmlString {
                return new HtmlString(__('user-race-profile.actions.export.modal_description'));
            })
            ->modalSubmitActionLabel(__('user-race-profile.actions.export.modal_submit'))
            ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin, AppRoles::EventMaster, AppRoles::EventOrganizer, AppRoles::BillingSpecialist]))
            ->schema([
                Grid::make(1)
                    ->schema([
                        Select::make('export_type')
                            ->label(__('user-race-profile.actions.export.export_type'))
                            ->options([
                                self::ALL_REGISTRATIONS => __('user-race-profile.actions.export.export_type_all'),
                                self::ACTIVE_REGISTRATIONS => __('user-race-profile.actions.export.export_type_active'),
                                self::DEACTIVATED_REGISTRATIONS => __('user-race-profile.actions.export.export_type_deactivated'),
                            ])
                            ->required(),
                    ]),

            ]);
    }

    private static function getNotificationMessage(): Notification
    {
        return Notification::make()
            ->title(__('user-race-profile.actions.export.notification_title'))
            ->body(__('user-race-profile.actions.export.notification_body'))
            ->success()
            ->seconds(15)
            ->send();
    }
}
