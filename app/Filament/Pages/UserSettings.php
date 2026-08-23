<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Support\Enums\Width;
use App\Enums\AppRoles;
use App\Enums\UserParamType;
use App\Filament\Pages\Actions\UserChangePassword;
use App\Filament\Pages\Actions\UserSendMail;
use App\Filament\Widgets\AppVersion;
use App\Filament\Widgets\PostsOverview;
use App\Filament\Widgets\StatsOverview;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Filament\Actions\ActionGroup;

class UserSettings extends Page
{
    use HasPageShield;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-home';

    protected string $view = 'filament.pages.user-settings';
    protected static ?string $slug = 'user-overview';


    public function getMaxContentWidth(): Width
    {
        return Width::Full;
    }

    public function getHeading(): string
    {
        return __('filament/user-setting.heading');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PostsOverview::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            StatsOverview::class,
            AppVersion::class,
        ];
    }

    public function render(): View
    {
        $userId = Auth()->user()?->id;
        /** @var User $user */
        $user = User::query()->findOrFail($userId);
        $usersAmountCount = $user->getParam(UserParamType::UserActualBalance);

        if ($usersAmountCount === null) {
            $usersAmountCount = DB::table('user_credits')
                ->where('user_id', '=', $userId)
                ->select(['amount'])
                ->sum('amount');

            $user->setParam(UserParamType::UserActualBalance, $usersAmountCount);
        }

        return view($this->getView(), [
                'usersAmountCount' => $usersAmountCount
            ])
            ->layout($this->getLayout(), [
                'livewire' => $this,
                'maxContentWidth' => $this->getMaxContentWidth(),
                ...$this->getLayoutData(),
            ]);
    }

    public static function getNavigationLabel(): string
    {
        return __('filament/user-setting.heading');
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

    public function getHeaderWidgetsColumns(): int|array
    {
        return 3;
    }

    public function getFooterWidgetsColumns(): int|array
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
            ->label(__('app.common.actions'));
    }
}
