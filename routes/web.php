<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\WordsController;
use App\Http\Controllers\PlayerController;

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

Route::middleware([])->group(function() {
    Route::get('/', function() {
        return Inertia::render('Home');
    });

    Route::middleware(['app.setting:app_status'])->group(function() {
        Route::get('/register', [PlayerController::class, 'home'])->middleware(['app.setting:player_registration'])->name('home');
        Route::get('/{player}/game', [PlayerController::class, 'gamePage'])->name('game');

        Route::middleware(['player.identified'])->group(function() {
            Route::post('/start-game', [PlayerController::class, 'startGame'])->name('startGame');
            Route::post('/game-action', [PlayerController::class, 'gameAction'])->middleware(['game.ongoing']);
        });
    });

    Route::get('/leaderboard', [PlayerController::class, 'leaderboard']);

    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        Route::get('/words', [WordsController::class, 'index'])->name('words');
        Route::get('/players', [PlayerController::class, 'index'])->name('players');
    });
});
