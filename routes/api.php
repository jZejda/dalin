<?php

use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\SportEventController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\UserEntryController;
use App\Http\Controllers\Ical\CalendarController;
use App\Models\User;
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

Route::get('/feed/kalendar/zavody/me/{token}', [CalendarController::class, 'personalRaceCalendar'])
    ->middleware('throttle:30,1');

Route::get('/feed/kalendar/treninky/me/{token}', [CalendarController::class, 'personalTrainingCalendar'])
    ->middleware('throttle:30,1');

// User API protected by x-apikey and member role
Route::prefix('v1/user')->middleware([
    'apikey',
    'role:' . User::ROLE_MEMBER,
])->group(function (): void {
    //Route::get('/', [UserController::class, 'show']);
    Route::get('/race-profiles', [UserController::class, 'raceProfiles']);
    Route::get('/entry', [UserController::class, 'entry']);
    Route::post('/entry', [UserEntryController::class, 'store']);
    Route::delete('/entry/{userEntry}', [UserEntryController::class, 'destroy']);
    Route::get('/credit-balance', [UserController::class, 'creditBalance']);
});

// Sport events readable by any member — required for the API entry flow
Route::prefix('v1')->middleware([
    'apikey',
    'role:' . User::ROLE_MEMBER
    . '|' . User::ROLE_REDACTOR
    . '|' . User::ROLE_SUPER_ADMIN
    . '|' . User::ROLE_EVENT_MASTER,
])->group(function (): void {
    Route::get('/sport-event', [SportEventController::class, 'list']);
    Route::get('/sport-event/{sportEvent}', [SportEventController::class, 'detail']);
});

// Posts API protected by x-apikey and role permission using spatie/permission ( OR Permission)
Route::prefix('v1')->middleware([
    'apikey',
    'role:' . User::ROLE_REDACTOR
    . '|' . User::ROLE_SUPER_ADMIN
    . '|' . User::ROLE_EVENT_MASTER,
])->group(function () {
    Route::get('/post', [PostController::class, 'list']);
    Route::get('/post/{post}', [PostController::class, 'detail']);
    Route::post('/post', [PostController::class, 'store']);
    Route::put('/post/{post}', [PostController::class, 'update']);

    Route::get('/page', [PageController::class, 'list']);
    Route::get('/page/{page}', [PageController::class, 'detail']);
});
