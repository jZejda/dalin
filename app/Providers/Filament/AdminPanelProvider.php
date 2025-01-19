<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\Widgets\PostsOverview;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\View\View;
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
            ->maxContentWidth('full')
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
            ->middleware([
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
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
                ->url(fn (): string => 'user-setting')
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
