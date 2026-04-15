<?php

use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\SportEventController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Ical\CalendarController;
use App\Models\User;
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

Route::middleware('auth:sanctum')->prefix('user')->group(function (): void {
    Route::get('/', function (Request $request) {
        return $request->user();
    });
    Route::get('/race-profiles', [UserController::class, 'raceProfiles']);
    Route::get('/entry', [UserController::class, 'entry']);
    Route::get('/credit-balance', [UserController::class, 'creditBalance']);
});

// Posts API protected by x-apikey and role permission using spatie/permission ( OR Permission)
Route::prefix('v1')->middleware([
    'apikey',
    'role:' . User::ROLE_REDACTOR
    . '|' . User::ROLE_SUPER_ADMIN
    . '|' . User::ROLE_EVENT_MASTER,
])->group(function () {
    Route::get('/post/list', [PostController::class, 'list']);
    Route::get('/post/{post}', [PostController::class, 'detail']);

    Route::get('/page/list', [PageController::class, 'list']);
    Route::get('/page/{page}', [PageController::class, 'detail']);

    Route::get('/sport-event/list', [SportEventController::class, 'list']);
});
