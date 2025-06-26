<?php

use App\Http\Controllers\AchievementsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\PlayerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {

    Route::view('add', 'dashboard');

    Route::controller(GameController::class)->group(function () {
        Route::post('/game-logic', 'gamelogic');
        Route::post('/startGame', 'startGame');
    });

    Route::controller(DashboardController::class)->group(function () {
        Route::post('save_game_results', 'save_game_results');
        Route::get('/api/dashboard', 'index')->name('api.dashboard');
    });

    Route::controller(PlayerController::class)->group(function () {
        Route::get('/{game_id?}', 'index')->name('dashboard');
        Route::post('add', 'dashboard');
        Route::post('/save_game_id', 'save_game_id');
    });

    Route::controller(LeaderboardController::class)->group(function () {
        Route::get('/leaderboard', 'getLeaderBoard')->name('leaderboard');
        Route::post('/game-result', 'storeResult');
    });

    Route::controller(AchievementsController::class)->group(function () {
        Route::get('/awardAchievement', 'awardAchievement')->name('awardAchievement');
        Route::get('/achievements', 'index')->name('achievements');
    });
});
