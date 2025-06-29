<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\AppRoles;
use App\Filament\Pages\Actions\ExportUserRaceProfileData;
use App\Shared\Helpers\AppHelper;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class UserRaceProfileList extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $title = 'Registrace';
    protected static ?string $navigationGroup = 'Správa';
    protected static ?int $navigationSort = 34;

    protected static string $view = 'filament.pages.user-race-profile-list';


    protected function getHeaderActions(): array
    {
        return [
            Action::make('help')
                ->label('Nápověda')
                ->icon('heroicon-o-information-circle')
                ->color('gray')
                ->url(AppHelper::getPageHelpUrl('stranka-registrace.html')),
            (Auth::user()?->hasRole([
                AppRoles::EventMaster,
                AppRoles::EventOrganizer,
                AppRoles::BillingSpecialist,
                AppRoles::SuperAdmin,
            ])) ? $this->getGroupActions() : ActionGroup::make([]),
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 3;
    }

    protected function getGroupActions(): ActionGroup
    {
        return ActionGroup::make(
            [
                ExportUserRaceProfileData::makeExport(),
            ]
        )->button()
            ->icon('heroicon-s-chevron-double-down')
            ->color('primary')
            ->label('Akce');
    }
}
