<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\EventController;
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
Route::prefix('auth')->group(function () {
    // Login Route
    Route::post('login', [AuthenticationController::class, 'login'])
        ->name('login');

    // Register Route
    Route::post('register', [AuthenticationController::class, 'register'])
        ->name('register');

    Route::middleware('jwt')->group(function () {
        // Refresh Token Route
        Route::post('refresh', [AuthenticationController::class, 'refresh'])
            ->name('refresh');

        // Logout Route
        Route::post('logout', [AuthenticationController::class, 'logout'])
            ->name('logout');
    });

});

Route::prefix('event')->middleware(['jwt'])->group(function () {

    // Store Route
    Route::post('create', [EventController::class, 'store'])
        ->name('event.store');

    // Get Event
    Route::get('index', [EventController::class, 'index'])
        ->name('event.index');

    // Show Event
    Route::get('show/{event}', [EventController::class, 'show'])
        ->name('event.show');

    // Update a specific event
    Route::put('update/{event}', [EventController::class, 'update'])
        ->name('event.update');

    // Delete Event
    Route::delete('delete/{event}', [EventController::class, 'delete'])
        ->name('event.delete');

    // Get Location btw start date and end date
    Route::get('location', [EventController::class, 'getLocationDateRange'])
        ->name('event.location');

});
