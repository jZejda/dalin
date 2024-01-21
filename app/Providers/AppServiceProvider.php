<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Facades\Filament;
use Illuminate\Foundation\Vite;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //        Curator::navigationGroup('Obsah')
        //        ->navigationSort(69);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // TODO remove after release product db mysql up 5.7
        // setting for older version db
        // Schema::defaultStringLength(191);

        //        FilamentAsset::register([
        //            //Css::make('custom-stylesheet', __DIR__ . '/../../resources/css/custom.css'),
        //            Css::make('custom-stylesheet', __DIR__ . '/../../resources/css/custom.css'),
        //        ]);
        Filament::serving(function () {
            // Using Vite
            Filament::registerTheme(
                app(Vite::class)('resources/css/app.css'),
            );
        });
    }
}
