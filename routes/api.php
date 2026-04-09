<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PlayerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/gamelogic', [GameController::class, 'gamelogic']);
Route::post('/save_game_results', [DashboardController::class, 'save_game_results']);

Route::post('/games/start', [GameController::class, 'startGame']);
Route::post('/games/logic', [GameController::class, 'gamelogic']);
Route::post('/games/results', [DashboardController::class, 'save_game_results']);
Route::get('/leaderboard', [LeaderboardController::class, 'apiLeaderboard']);
Route::put('/players/{game_id}', [PlayerController::class, 'updateByGameId']);
Route::delete('/players/{game_id}', [PlayerController::class, 'deleteByGameId']);
