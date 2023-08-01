<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/v1/events', [\App\Http\Controllers\API\EventsController::class, 'events']);
Route::post('/v1/login', [\App\Http\Controllers\API\EventsController::class, 'login']);
Route::get('/v1/organizers/{organizerslug}/events/{eventslug}', [\App\Http\Controllers\API\EventsController::class, 'view']);

Route::middleware('auth.api')->group(function() {
    Route::post('/v1/logout', [\App\Http\Controllers\API\EventsController::class, 'logout']);
    Route::post('/v1/organizers/{organizerslug}/events/{eventslug}/registration', [\App\Http\Controllers\API\EventsController::class, 'register']);
    Route::get('/v1/registrations', [\App\Http\Controllers\API\EventsController::class, 'registrations']);
});
