<?php

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);
beforeEach(function () {
    $this->user = User::factory()->create(['rank' => 100]);

    // Set up a player board state in the DB

});
it('detects the winner Tester', function () {
    Player::create([
        'player' => 'X',
        'gameOver' => false,
        'data' => json_encode([
            'X',
            'X',
            '', // Player is about to win with index 2
            '',
            'O',
            '',
            '',
            '',
            'O',
        ]),
    ]);

    $response = postJson('/api/gamelogic', [
        'index' => 2, // Final move for player to win
        'gameOver' => 0,
        'aisymbol' => 'O',
        'playerNameFromServer' => 'Tester',
        'id' => $this->user->id,
        'difficulty' => 'hard',
    ]);

    $response->assertJson([
        'gameOver' => true,
        'winner' => 'Tester',
    ]);
});
it('detects draw correctly', function () {
    Player::create([
        'player' => 'X',
        'gameOver' => false,
        'data' => json_encode([
            'X',
            'O',
            'X',
            'O',
            'O',
            'X',
            '',
            'X',
            'O',
        ]),
    ]);
    $response = postJson('/api/gamelogic', [
        'index' => 6,
        'gameOver' => 0,
        'aisymbol' => 'O',
        'playerNameFromServer' => 'Tester',
        'id' => $this->user->id,
        'difficulty' => 'hard',
    ]);
    $response->assertJson([
        'gameOver' => true,
        'isDraw' => true,
    ]);
});
it('detects the winner AI', function () {
    Player::create([
        'player' => 'X',
        'gameOver' => false,
        'data' => json_encode([
            'X',
            'X',
            '',
            'X',
            'O',
            '',
            'O',
            'O',
            'X',
        ]),
    ]);
    $response = postJson('/api/gamelogic', [
        'index' => 5,
        'gameOver' => 0,
        'aisymbol' => 'O',
        'playerNameFromServer' => 'Tester',
        'id' => $this->user->id,
        'difficulty' => 'hard',
    ]);

    $response->assertJson([
        'gameOver' => true,
        'winner' => 'AI',
    ]);
});
