<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\AppSetting;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WordsController;
use App\Http\Controllers\WordsetController;
use App\Http\Controllers\EventController;
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
    $domain = config('app.domain');
    Route::domain("{event:slug}.{$domain}")->group(function () {
        Route::get('/', [HomeController::class, 'home'])->name('home');
        Route::get('/leaderboard', [PlayerController::class, 'leaderboard'])->middleware(['app.setting:show_leaderboard'])->name('leaderboard');

        Route::middleware(['app.setting:app_status'])->group(function() {
            Route::get('/register', [PlayerController::class, 'home'])->middleware(['app.setting:player_registration'])->name('player-register');
            Route::get('/{player}/game', [PlayerController::class, 'gamePage'])->name('game');

            Route::middleware(['player.identified'])->group(function() {
                Route::post('/start-game', [PlayerController::class, 'startGame'])->name('startGame');
                Route::post('/game-action', [PlayerController::class, 'gameAction'])->middleware(['game.ongoing']);
            });
        });
    });

    Route::get('/', [HomeController::class, 'adminHome'])->name('admin-home');

    Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
        Route::get('/words', [WordsController::class, 'index'])->name('words');
        Route::get('/wordsets', [WordsetController::class, 'index'])->name('wordsets');
        Route::get('/events', [EventController::class, 'index'])->name('events');

        $domain = config('app.domain');
        Route::domain("{event:slug}.{$domain}")->group(function () {
            Route::get('/players', [PlayerController::class, 'index'])->name('players');
        });
    });

    Route::get('/tech-wall', [WordsController::class, 'techWall'])->name('tech-wall');
});
