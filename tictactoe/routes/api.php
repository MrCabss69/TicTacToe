<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
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


// En api.php
Route::middleware('web')->group(function () {
    Route::post('/game/start', [GameController::class, 'start']);
    Route::post('/game/move', [GameController::class, 'move']);
    Route::get('/game/state/{game_id}', [GameController::class, 'getState']);
    Route::post('/game/all_ids', [GameController::class,'printAllSessionGameIds']);
    Route::post('/game/bot-move/{gameId}', [GameController::class, 'requestBotMove']);
});
