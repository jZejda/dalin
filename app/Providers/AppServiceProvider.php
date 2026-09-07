<?php

declare(strict_types=1);

namespace App\Providers;

use App\Mcp\Servers\DalinServer;
use App\Services\AppVersionService;
use App\Models\AppSetting;
use App\Models\UserCredit;
use App\Observers\UserCreditObserver;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Facades\Filament;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Font;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Laravel\Mcp\Facades\Mcp;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Foundation\Vite;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Club settings from the admin panel override config/site-config.php;
        // rescue() covers fresh installs where app_settings does not exist yet.
        rescue(static fn () => AppSetting::applyClubConfigOverrides(), report: false);

        // Verze a sestavení v `php artisan about` (a tedy i v deploy tasku app:version)
        AboutCommand::add('DaLin', fn (): array => [
            'Version' => app(AppVersionService::class)->summary(),
        ]);

        // MCP server registration
        Mcp::local('dalin', DalinServer::class);

        // CZ/EN language switch in the panel; preference persisted via
        // App\Listeners\PersistUserLocale (auto-discovered LocaleChanged listener)
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch): void {
            $switch
                ->locales(['cs', 'en'])
                ->visible(outsidePanels: true)
                ->userPreferredLocale(fn (): ?string => auth()->user()?->locale);
        });

        // Observer from EventServiceProvider
        UserCredit::observe(UserCreditObserver::class);
        // Outgoing e-mails are logged via App\Listeners\LogSentMail
        // (auto-discovered listener on the MessageSent event).

        // Rate limiting from RouteServiceProvider
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Class aliases for Blade templates (lazy-loaded, no auto-discovery)
        $loader = AliasLoader::getInstance();
        $loader->alias('Markdown', Markdown::class);
        $loader->alias('QrCode', QrCode::class);
        $loader->alias('Excel', Excel::class);

        Filament::serving(function () {
            // Using Vite
            Filament::registerTheme(
                app(Vite::class)('resources/css/app.css'),
            );
        });

        // Self-hosted Poppins (Latin + Latin Extended, weights 400-700); published to
        // public/fonts/app/poppins via `php artisan filament:assets`. Panel font family
        // is set via ->font() in AdminPanelProvider.
        FilamentAsset::register([
            Font::make('poppins', resource_path('fonts/poppins')),
        ]);

        // Used by resources/views/filament/forms/components/location-picker.blade.php. A plain
        // <script> tag in that view would never execute, since the view is rendered into an
        // action modal that Livewire injects into the DOM dynamically, and browsers don't run
        // <script> tags added that way — so the view loads the JS itself via a manually created
        // <script> element with a real load-completion promise (Filament's own x-load-js only
        // *starts* the fetch, it doesn't reliably block init() until the script has executed).
        // getScriptSrc('leaflet') below just resolves the CDN URL/version from one place; the
        // CSS has no init-order concerns so it's loaded globally as normal.
        FilamentAsset::register([
            Css::make('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
            Js::make('leaflet', 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js')->loadedOnRequest(),
        ]);
    }
}
