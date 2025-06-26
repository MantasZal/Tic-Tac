<?php

use App\Models\Player;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

test('player cannot make two moves in a row', function () {
    // Create a user
    $user = User::factory()->create();
    $this->actingAs($user);

    // Simulate an empty game board
    $board = array_fill(0, 9, '');
    Player::create([
        'player' => 'X',
        'gameOver' => false,
        'data' => json_encode($board),
    ]);

    // First move (valid)
    $response1 = postJson('/game-logic', [
        'index' => 0,
        'current' => 'X',
        'gameOver' => false,
        'aisymbol' => 'O',
        'playerNameFromServer' => $user->name,
        'id' => $user->id,
    ]);

    $response1->assertStatus(200);
    $response1->assertJsonMissing(['invalid' => true]);

    // Second move (player tries to move again immediately)
    // This will attempt to move at index 1 before AI does
    $response2 = postJson('/game-logic', [
        'index' => 1,
        'current' => 'X',
        'gameOver' => false,
        'aisymbol' => 'O',
        'playerNameFromServer' => $user->name,
        'id' => $user->id,
    ]);

    // This second move should be invalid if game state was updated correctly
    $response2->assertStatus(200);
    $response2->assertJson([
        'invalid' => true,
    ]);
});
