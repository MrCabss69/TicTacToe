<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;

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
    return view('welcome');
});


# GAME VIEW
Route::get( '/', [GameController::class, 'show']);
Route::get('/game', [GameController::class, 'show']);
Route::post('/game/start', [GameController::class, 'start']);
Route::post('/game/move', [GameController::class, 'move'])->name('game.move');

?>