<?php

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


/**
 * Frontend routes.
 */

Route::get('/', 'App\Http\Controllers\FrontEndController@index');
Route::get('/novinka/{id}', 'App\Http\Controllers\FrontEndController@novinka');
Route::get('novinky/vse', 'App\Http\Controllers\FrontEndController@postsAll');

Route::get('/stranka/{slug}', 'App\Http\Controllers\FrontEndController@stranka');

Route::get('vysledky/{id}', 'App\Http\Controllers\FrontEndController@oeventResult');

Route::prefix('cron')->group(function () {
    Route::get('/forecast/yoursecretkey/check', 'App\Http\Controllers\Cron\ServiceAppController@legForecast');
});

Route::middleware('throttle:20|60,1')->prefix('app-api')->group(function () {
    Route::get('/event-ifoxml-result/{id}', 'App\Http\Controllers\Tool\EventResultController@eventIofv3Result' );
});


Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


/*

Route::get('/', function () {
    return view('welcome');
});
 */
