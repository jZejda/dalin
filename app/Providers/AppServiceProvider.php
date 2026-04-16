<?php

declare(strict_types=1);

namespace App\Providers;

use App\Mcp\Servers\DalinServer;
use App\Models\UserCredit;
use App\Observers\UserCreditObserver;
use Filament\Facades\Filament;
use Laravel\Mcp\Facades\Mcp;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\AliasLoader;
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
        // MCP server registration
        Mcp::local('dalin', DalinServer::class);

        // Observer from EventServiceProvider
        UserCredit::observe(UserCreditObserver::class);

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
