<?php

declare(strict_types=1);

namespace App\Filament\Pages\Actions;

use App\Enums\AppRoles;
use App\Http\Controllers\UserRaceProfileController;
use Filament\Forms\Components\Grid;
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
                    self::getNotificatinMessage();
                    return (new UserRaceProfileController())->export(self::ALL_REGISTRATIONS);
                } elseif ($data['export_type'] === self::ACTIVE_REGISTRATIONS) {
                    self::getNotificatinMessage();
                    return (new UserRaceProfileController())->export(self::ACTIVE_REGISTRATIONS);
                } elseif ($data['export_type'] === self::DEACTIVATED_REGISTRATIONS) {
                    self::getNotificatinMessage();
                    return (new UserRaceProfileController())->export(self::DEACTIVATED_REGISTRATIONS);
                } else {
                    return null;
                }
            })
            ->color('gray')
            ->label('Export do Excelu')
            ->icon('heroicon-o-document-arrow-down')
            ->modalHeading('Vytvoří exportní soubor podle zadání')
            ->modalDescription(function (): HtmlString {
                return new HtmlString('Zvol požadovaný export. Je možné zvolit <strong>všechny registrace</strong> smazané i nesmazané</br>
                Nebo pouze <strong>aktivní</strong> případně <strong>neaktní</strong>.');
            })
            ->modalSubmitActionLabel('Exportovat')
            ->visible(auth()->user()->hasRole([AppRoles::SuperAdmin->value, AppRoles::EventMaster->value]))
            ->form([
                Grid::make(1)
                    ->schema([
                        Select::make('export_type')
                            ->label('Nabízené exporty')
                            ->options([
                                self::ALL_REGISTRATIONS => 'Všechny registrace *.xlsx',
                                self::ACTIVE_REGISTRATIONS => 'Pouze aktivní registrace *.xlsx',
                                self::DEACTIVATED_REGISTRATIONS => 'Neaktivní registrace *.xlsx',
                            ])
                            ->required(),
                    ]),

            ]);
    }

    private static function getNotificatinMessage(): Notification
    {
        return Notification::make()
            ->title('Export přihlášek proběhl v pořádku')
            ->body('Souboru excelu přihlášených uživatelů otevřete z disku.')
            ->success()
            ->seconds(15)
            ->send();
    }
}
