<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AchievementsController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GameController;

Route::middleware(['auth'])->group(function () {

    Route::view('add', 'dashboard');
    Route::post('add', [PlayerController::class, 'dashboard']);
    Route::post('/ai-move', [OpenAIController::class, 'aiMove']);
    // Route::post('/users-rank', [UserController::class, 'update']);
    Route::get('/leaderboard', [LeaderboardController::class, 'getLeaderBoard'])->name('leaderboard');
    Route::get('/achievements', [AchievementsController::class, 'index'])->name('achievements');
    Route::get('/awardAchievement', [AchievementsController::class, 'awardAchievement'])->name('awardAchievement');
    Route::post('/game-result', [LeaderboardController::class, 'storeResult']);
    Route::get('/', [PlayerController::class, 'index'])->name('dashboard');
    Route::get('/api/dashboard', [DashboardController::class, 'index'])->name('api.dashboard');
    Route::post('/validate-move', [GameController::class, 'validateMove']);
    Route::post('/check-game-over', [GameController::class, 'checkGameOver']);
    Route::post('/update-rank', [GameController::class, 'updateRank']);
    Route::post('/game-logic', [GameController::class, 'gamelogic']);
});
