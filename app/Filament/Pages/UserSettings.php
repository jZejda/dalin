<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Enums\AppRoles;
use App\Filament\Pages\Actions\UserChangePassword;
use App\Filament\Pages\Actions\UserSendMail;
use App\Filament\Widgets\PostsOverview;
use App\Filament\Widgets\StatsOverview;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Filament\Actions\ActionGroup;

class UserSettings extends Page
{
    use HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static string $view = 'filament.pages.user-settings';
    protected static ?string $slug = 'user-setting';


    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::Full;
    }

    public function getHeading(): string
    {
        return __('Můj přehled');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PostsOverview::class,
            StatsOverview::class,
        ];
    }

    public function render(): View
    {
        $usersAmountCount = DB::table('user_credits')
            ->where('user_id', '=', Auth()->user()?->id)
            ->select(['amount'])
            ->sum('amount');

        return view(static::$view, [
                'usersAmountCount' => $usersAmountCount
            ])
            ->layout(static::$layout, [
                'livewire' => $this,
                'maxContentWidth' => $this->getMaxContentWidth(),
                ...$this->getLayoutData(),
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('Můj přehled');
    }

    protected function getHeaderActions(): array
    {
        return [
            UserChangePassword::getAction(),
            (Auth::user()?->hasRole([
                AppRoles::EventMaster->value,
                AppRoles::BillingSpecialist->value,
                AppRoles::Redactor->value,
                AppRoles::SuperAdmin->value,
            ])) ? $this->getGroupActions() : ActionGroup::make([]),
        ];
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 3;
    }

    protected function getGroupActions(): ActionGroup
    {
        return ActionGroup::make([
            UserSendMail::getAction(),
        ])->button()
            ->icon('heroicon-s-chevron-double-down')
            ->color('gray')
            ->label('Actions');
    }
}
