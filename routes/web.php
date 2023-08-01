<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if(\Illuminate\Support\Facades\Auth::check()) {
        return redirect('/events');
    }

    return redirect('/login');
});

Route::get('/login', [\App\Http\Controllers\UserController::class, 'login']);
Route::post('/login', [\App\Http\Controllers\UserController::class, 'postLogin']);

Route::middleware('auth')->group(function() {
    Route::get('/signout', [\App\Http\Controllers\UserController::class, 'logout']);
    Route::get('/events', [\App\Http\Controllers\EventsController::class, 'index']);
    Route::get('/events/create', function() {
        return view('create-event');
    });
    Route::post('/events/create', [\App\Http\Controllers\EventsController::class, 'create']);
    Route::get('/event/{id}', [\App\Http\Controllers\EventsController::class, 'one']);
    Route::get('/event/{id}/edit', [\App\Http\Controllers\EventsController::class, 'editEventView']);
    Route::post('/event/{id}/edit', [\App\Http\Controllers\EventsController::class, 'editEvent']);

    Route::get('/event/{id}/capacity', [\App\Http\Controllers\EventsController::class, 'roomCapacity']);

    // new channel routes
    Route::get('/event/{id}/channel', [\App\Http\Controllers\EventsController::class, 'newChannelView']);
    Route::post('/event/{id}/channel', [\App\Http\Controllers\EventsController::class, 'newChannel']);

    // new ticket routes
    Route::get('/event/{id}/ticket', [\App\Http\Controllers\EventsController::class, 'newTicketView']);
    Route::post('/event/{id}/ticket', [\App\Http\Controllers\EventsController::class, 'newTicket']);

    // new room routes
    Route::get('/event/{id}/room', [\App\Http\Controllers\EventsController::class, 'newRoomView']);
    Route::post('/event/{id}/room', [\App\Http\Controllers\EventsController::class, 'newRoom']);

    // new session routes
    Route::get('/event/{id}/session', [\App\Http\Controllers\EventsController::class, 'newSessionView']);
    Route::post('/event/{id}/session', [\App\Http\Controllers\EventsController::class, 'newSession']);
    Route::get('/event/{id}/session/{session_id}', [\App\Http\Controllers\EventsController::class, 'editSessionView']);
    Route::post('/event/{id}/session/{session_id}', [\App\Http\Controllers\EventsController::class, 'editSession']);
});
