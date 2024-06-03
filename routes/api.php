<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\WordsController;
use App\Http\Controllers\WordsetController;
use App\Http\Controllers\PlayerController;
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

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/word/store', [WordsController::class, 'store'])->name('create-word');
    Route::post('/word/{word}/update', [WordsController::class, 'update'])->name('update-word');
    Route::post('wordset/store', [WordsetController::class, 'store'])->name('create-wordset');
    Route::post('wordset/{wordset}/update', [WordsetController::class, 'update'])->name('update-wordset');
    Route::post('event/store', [EventController::class, 'store'])->name('store-event');
    Route::post('event/{event}/update', [EventController::class, 'update'])->name('update-event');

    Route::prefix("{event:slug}")->group(function () {
        Route::post('/toggle-setting', [WordsController::class, 'toggleSetting'])->name('toggle-setting');
        Route::post('/{player}/send-invite', [PlayerController::class, 'sendInvite'])->name('send-invite');
        Route::post('/{player}/reset-game', [PlayerController::class, 'resetGame'])->name('reset-game');
    });
});

Route::prefix("{event:slug}")->group(function () {
    Route::post('/register', [PlayerController::class, 'register'])->middleware(['app.setting:player_registration'])->name('register-api');
});

