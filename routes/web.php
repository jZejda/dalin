<?php

use App\Http\Controllers\Cron\CommonCron;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\ResultListController;
use App\Http\Controllers\Frontend\StartListController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserEntryController;
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

Route::get('/', function () {
    return view('welcome', ['sponsorSectionId' => 0]);
});

//Route::get('/email/', function(){
//    return new AddUpdateSportEvent();
//});

Route::get('/cron-scheduler/'.config('site-config.cron_url_key'), function () {
    Artisan::call('schedule:run');
});

Route::get('/cron-hourly/'.config('site-config.cron_hourly.url_key'), [CommonCron::class, 'runHourly']);

Route::get('/novinka/{id}', [PostController::class, 'post']);
Route::get('/stranka/{slug}', [PageController::class, 'page']);
Route::get('/startovka/{slug}', [StartListController::class, 'singleStartList']);
Route::get('/vysledky/{slug}', [ResultListController::class, 'singleResultList']);

// Route::get('/akce/{id}', [ResultListController::class, 'singleResultList']);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('admin')->group(function () {
    Route::get('/export/event-entry/{eventId}', [UserEntryController::class, 'export']);
})->middleware(['auth', 'verified']);

Route::get('/admin/test', [TestController::class, 'test']);
//Route::get('/admin/webhook', [DiscordRaceEventNotification::class, 'notification']);

require __DIR__.'/auth.php';
