<?php

use App\Http\Controllers\WordsController;
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

    Route::post('/word/store', [WordsController::class, 'store']);
    Route::post('/word/{word}/update', [WordsController::class, 'update']);
    Route::post('/toggle-setting', [WordsController::class, 'toggleSetting']);
    Route::post('/{player}/send-invite', [PlayerController::class, 'sendInvite']);
    Route::post('/{player}/reset-game', [PlayerController::class, 'resetGame']);
});

Route::post('/register', [PlayerController::class, 'register'])->middleware(['app.setting:player_registration']);
