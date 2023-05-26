<?php

use App\Http\Controllers\Cron\CommonCron;
use App\Http\Controllers\TestController;
use App\Http\Livewire\Frontend\ShowPage;
use App\Http\Livewire\Frontend\ShowPost;
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

Route::get('/cron-scheduler/' . env('CRON_URL_KEY', 'key'), function () {
    Artisan::call('schedule:run');
});

Route::get('/cron-hourly/' . env('CRON_HOURLY_URL_KEY', 'hourly_key'), [CommonCron::class, 'runHourly']);

Route::get('/novinka/{id}', ShowPost::class);
Route::get('/stranka/{slug}', ShowPage::class);


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::get('/admin/test', [TestController::class, 'test']);
//Route::get('/admin/webhook', [DiscordRaceEventNotification::class, 'notification']);


require __DIR__.'/auth.php';
