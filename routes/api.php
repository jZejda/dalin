<?php

use App\Http\Controllers\Ical\CalendarController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/feed/kalendar/zavody/all/', [CalendarController::class, 'raceCalendar'])
    ->middleware('throttle:30,1');

Route::get('/feed/kalendar/treninky/all/', [CalendarController::class, 'trainingCalendar'])
    ->middleware('throttle:30,1');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
