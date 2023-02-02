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

Route::get('/', [PlayerController::class, 'home'])->name('home');
Route::post('/identify-player', [PlayerController::class, 'identifyPlayer']);

Route::middleware(['player.identified'])->group(function() {
    Route::get('/game', [PlayerController::class, 'gamePage'])->name('game');
    Route::post('/start-game', [PlayerController::class, 'startGame'])->name('startGame');
    Route::post('/game-action', [PlayerController::class, 'gameAction']);
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])->group(function () {
    Route::get('/words', [WordsController::class, 'index'])->name('words');
});
