<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\UserCredit;
use App\Observers\UserCreditObserver;
use Filament\Facades\Filament;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Vite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        // Observer from EventServiceProvider
        UserCredit::observe(UserCreditObserver::class);

        // Rate limiting from RouteServiceProvider
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Class aliases for Blade templates (lazy-loaded, no auto-discovery)
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Markdown', \Illuminate\Mail\Markdown::class);
        $loader->alias('QrCode', \SimpleSoftwareIO\QrCode\Facades\QrCode::class);
        $loader->alias('Excel', \Maatwebsite\Excel\Facades\Excel::class);

        Filament::serving(function () {
            // Using Vite
            Filament::registerTheme(
                app(Vite::class)('resources/css/app.css'),
            );
        });
    }
}
