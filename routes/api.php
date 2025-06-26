<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/gamelogic', [GameController::class, 'gamelogic']);
Route::post('/save_game_results', [DashboardController::class, 'save_game_results']);
