<?php

use App\Http\Controllers\Cron\CommonCron;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserEntryController;
use App\Http\Livewire\Frontend\ResultList;
use App\Http\Livewire\Frontend\ShowPage;
use App\Http\Livewire\Frontend\ShowPost;
use App\Http\Livewire\Frontend\StartList;
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
    return view('welcome');
});

//Route::get('/email/', function(){
//    return new AddUpdateSportEvent();
//});

Route::get('/cron-scheduler/' . config('site-config.cron_url_key'), function () {
    Artisan::call('schedule:run');
});

Route::get('/cron-hourly/' . config('site-config.cron_hourly.url_key'), [CommonCron::class, 'runHourly']);

Route::get('/novinka/{id}', ShowPost::class);
Route::get('/stranka/{slug}', ShowPage::class);
Route::get('/startovka/{slug}', StartList::class);
Route::get('/vysledky/{slug}', ResultList::class);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::prefix('admin')->group(function () {
    Route::get('/export/event-entry/{eventId}', [UserEntryController::class, 'export']);
    Route::get('/export-users',[UserController::class,'exportUsers'])->name('export-users');
})->middleware(['auth', 'verified']);




Route::get('/admin/test', [TestController::class, 'test']);
//Route::get('/admin/webhook', [DiscordRaceEventNotification::class, 'notification']);


require __DIR__.'/auth.php';
