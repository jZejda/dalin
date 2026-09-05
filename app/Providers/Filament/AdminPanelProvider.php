<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Widgets\PostsOverview;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\FontProviders\LocalFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\View\View;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\View as FacadesView;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->maxContentWidth(Width::Full)
            // Self-hosted Poppins registered as a Filament asset in AppServiceProvider;
            // LocalFontProvider with no URL means we rely on that asset's @font-face
            // rules instead of fetching from an external CDN.
            ->font('Poppins', provider: LocalFontProvider::class)
//            ->brandName(config('site-config.club.abbr'))
            ->brandLogo(function (): ?View {
                $logoPath = 'filament.logo.' . strtolower(config('site-config.club.abbr')) . '-logo';
                if (FacadesView::exists($logoPath)) {
                    return view($logoPath);
                }

                return null;
            })
            ->sidebarCollapsibleOnDesktop()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
            // Pages\Dashboard::class,
            ])
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
            // Widgets\AccountWidget::class,
            // Widgets\FilamentInfoWidget::class,
            PostsOverview::class,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('300s')
            ->renderHook(PanelsRenderHook::BODY_START, fn (): View => view('demo.banner'))
            ->renderHook(PanelsRenderHook::AUTH_LOGIN_FORM_BEFORE, fn (): View => view('demo.login-credentials'))
            ->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            PreventRequestForgery::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
            Authenticate::class,
            ])
            ->userMenuItems([
            MenuItem::make()
                ->label('Můj přehled')
                ->url(fn (): string => 'user-overview')
                ->icon('heroicon-o-home'),
            ])
            ->plugins([
            FilamentShieldPlugin::make(),
//                CuratorPlugin::make()
//                    ->label('Media')
//                    ->pluralLabel('Media')
//                    ->navigationIcon('heroicon-o-photo')
//                    ->navigationGroup('Obsah')
//                    ->navigationSort(3)
//                    ->navigationCountBadge(),
                //->resource(CustomMediaResource::class)
            ]);
    }
}
