<?php

declare(strict_types=1);

use App\Http\Controllers\Cron\CommonCron;
use App\Http\Controllers\Demo\DemoResetController;
use App\Http\Controllers\Demo\DemoScreenshotModeController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\ResultListController;
use App\Http\Controllers\Frontend\StartListController;
use App\Http\Controllers\UserEntryController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::post('/register', function () {
    return redirect(url('/'));
});

// Sites without a public frontend (per-site `features.public_site.use_public_site`) send `/` straight to the admin login.
Route::get('/', function () {
    if (! config('site-config.features.public_site.use_public_site', true)) {
        return redirect()->route('filament.admin.auth.login');
    }

    return view('welcome', ['sponsorSectionId' => 0]);
});

Route::view('/design-system', 'design-system.showcase')->name('design-system');

Route::get('/cron-scheduler/'.config('site-config.cron_url_key'), function () {
    Artisan::call('schedule:run');
});

Route::get('/demo-reset/'.config('demo.reset_url_key'), [DemoResetController::class, 'reset']);

Route::get('/demo-screenshot-mode/'.config('demo.screenshot_mode_key'), [DemoScreenshotModeController::class, 'enable']);
Route::get('/demo-screenshot-mode-off/'.config('demo.screenshot_mode_key'), [DemoScreenshotModeController::class, 'disable']);

Route::get('/cron-hourly/'.config('site-config.cron_hourly.url_key'), [CommonCron::class, 'runHourly']);

Route::get('/novinky', [PostController::class, 'index'])->name('posts.index');
Route::get('/novinka/{id}', [PostController::class, 'post']);
Route::get('/stranka/{slug}', [PageController::class, 'page']);
Route::get('/startovka/{slug}', [StartListController::class, 'singleStartList']);
Route::get('/startovka/{slug}/bez-vakantu', [StartListController::class, 'downloadWithoutVakant']);
Route::get('/vysledky/{slug}', [ResultListController::class, 'singleResultList']);

Route::get('/akce/{id}', [\App\Http\Controllers\Frontend\SportEvent::class, 'singleEvent'])
    ->name('sport-event.show');

Route::get('/doprava/zadost/{transportRequest}/{decision}', \App\Http\Controllers\TransportRequestDecisionController::class)
    ->whereIn('decision', ['approve', 'reject'])
    ->middleware('signed')
    ->name('transport-request.decision');


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('admin')->group(function () {
    Route::get('/export/event-entry/{eventId}', [UserEntryController::class, 'export']);
    Route::get('/export/event-entry-iof/{eventId}', [UserEntryController::class, 'exportEntryListIofV3'])->name('admin.export.event-entry-iof');
    Route::get('/export/event-entry-csos/{eventId}', [UserEntryController::class, 'exportEntryListCsos'])->name('admin.export.event-entry-csos');
})->middleware(['auth', 'verified']);

//Route::get('/admin/webhook', [DiscordRaceEventNotification::class, 'notification']);

require __DIR__.'/auth.php';
