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
    }
}
