<?php

use App\Models\GameResult;
use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('Corect achievements', function () {
    $user = User::factory()->create(['rank' => 100]);
    $this->actingAs($user);

    GameResult::create(['user_id' => $user->id, 'won' => true]);

    Player::create([
        'player' => 'X',
        'gameOver' => true,
        'data' => json_encode([
            'X',
            'X',
            '',
            '',
            'O',
            '',
            '',
            '',
            'O',
        ]),
    ]);

    $lastGame = GameResult::where('user_id', $user->id)
        ->orderBy('id', 'desc')
        ->first();

    $achievements = $lastGame?->new_acievements;
    $response = $achievements ? json_decode($achievements, true) : [];

    expect($response)->not()->toBeNull();
});
